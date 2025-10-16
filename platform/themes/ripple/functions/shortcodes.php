<?php

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Blog\Models\Category;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use App\Models\Calendario;
use App\Models\FormationVote;
use App\Models\Player;
use App\Support\FormationStats;

app('events')->listen(RouteMatched::class, function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();



    // Shortcode::setPreviewImage(
    //     'diretta-link',
    //     Theme::asset()->url('images/ui-blocks/diretta-link.png')   // add any 300×200 png here
    // );
Shortcode::register(
    'formazione-risultati',
    __('Formazione – Risultati & Archivio'),
    __('Mostra: prossimo match (link al sondaggio), ultimo risultato votazione, e archivio.'),
    function (ShortcodeCompiler $sc) {

        $team = $sc->team ?: 'fiorentina';

        // 1) Next upcoming match (SCHEDULED or LIVE)
        $nextMatch = Calendario::whereIn('status', ['SCHEDULED','LIVE'])
            ->orderBy('match_date', 'asc')
            ->first();

        // 2) Last finished match
        $lastFinished = Calendario::where('status', 'FINISHED')
            ->orderBy('match_date', 'desc')
            ->first();

        // 3) Aggregate votes for last finished (if any)
        $aggregate = [
            'totalVotes'   => 0,
            'topFormation' => null,
            'slots'        => [], // slot => ['player'=>Player, 'votes'=>int, 'perc'=>float]
        ];

        if ($lastFinished) {
            $votes = FormationVote::where('match_id', $lastFinished->match_id)
                ->where('team', $team)
                ->get();

            $aggregate['totalVotes'] = $votes->count();

            // formation popularity
            $formationCounts = $votes->groupBy('formation')->map->count()->sortDesc();
            $aggregate['topFormation'] = $formationCounts->keys()->first();

            // per-slot tallies
            $slotTallies = [];
            foreach ($votes as $v) {
                foreach ((array) $v->positions as $slot => $playerId) {
                    $slotTallies[$slot][$playerId] = ($slotTallies[$slot][$playerId] ?? 0) + 1;
                }
            }

            // pick top per slot and resolve players
            $topBySlot = [];
            $playerIds = [];
            foreach ($slotTallies as $slot => $m) {
                arsort($m);
                $topId = array_key_first($m);
                $topBySlot[$slot] = ['player_id' => $topId, 'votes' => $m[$topId]];
                $playerIds[] = $topId;
            }

            $players = Player::whereIn('id', $playerIds)->get()->keyBy('id');

            foreach ($topBySlot as $slot => $info) {
                $p = $players->get($info['player_id']);
                $perc = $aggregate['totalVotes'] > 0
                    ? round(($info['votes'] / $aggregate['totalVotes']) * 100)
                    : 0;
                $aggregate['slots'][$slot] = [
                    'player' => $p,
                    'votes'  => $info['votes'],
                    'perc'   => $perc,
                ];
            }
        }

        // 4) Archive list (recent finished matches)
        $archive = Calendario::where('status', 'FINISHED')
            ->orderBy('match_date', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($m) use ($team) {
                $home = Arr::get(json_decode($m->home_team, true), 'name', '—');
                $away = Arr::get(json_decode($m->away_team, true), 'name', '—');
                $votes = FormationVote::where('match_id', $m->match_id)->where('team', $team)->count();
                return [
                    'match_id'   => $m->match_id,
                    'date'       => $m->match_date,
                    'home'       => $home,
                    'away'       => $away,
                    'votes'      => $votes,
                ];
            });

        return Theme::partial('shortcodes.formazione-risultati', [
            'team'         => $team,
            'nextMatch'    => $nextMatch,
            'lastFinished' => $lastFinished,
            'aggregate'    => $aggregate,
            'archive'      => $archive,
        ]);
    }
);

// (optional) Admin config: choose team
Shortcode::setAdminConfig('formazione-risultati', function (array $attrs) {
    return ShortcodeForm::createFromArray($attrs)
        ->withLazyLoading()
        ->add(
            'team',
            SelectField::class,
            SelectFieldOption::make()
                ->label(__('Squadra'))
                ->choices(['fiorentina' => 'Fiorentina', 'another' => 'Altro'])
                ->defaultValue('fiorentina')
                ->toArray()
        );
});


Shortcode::register(
    'nextmatch-votazione',
    __('Formazione – Votazione (prossima partita)'),
    __('Mostra il widget di votazione per la prossima partita (SCHEDULED/LIVE).'),
    function (ShortcodeCompiler $sc) {
        $team       = $sc->team ?: 'fiorentina';
        $formsCsv   = $sc->formations ?:'3-4-3,3-4-2-1,3-4-1-2,3-5-2 ,4-3-3,4-3-1-2,4-3-2-1,4-4-2,4-5-1,5-3-2,5-4-1';
        $formations = collect(explode(',', $formsCsv))
            ->map(fn($s) => trim($s))
            ->filter()->values()->all();

        // Prossima partita (SCHEDULED o LIVE)
        $match = Calendario::whereIn('status', ['SCHEDULED','LIVE'])
            ->orderBy('match_date', 'asc')
            ->first();

        // Giocatori raggruppati per ruolo (GK/DF/MF/FW)
        $players = Player::query()->orderBy('jersey_number')->get();
        $playersByRole = $players->groupBy(function ($p) {
            $pos = strtoupper(trim($p->position ?? ''));
            return match (true) {
                str_starts_with($pos, 'GK') || $pos === 'PORTIERE' || $pos === 'GOALKEEPER' => 'GK',
                str_starts_with($pos, 'DF') || $pos === 'DIFENSORE' || $pos === 'DEFENDER' => 'DF',
                str_starts_with($pos, 'MF') || $pos === 'CENTROCAMPISTA' || $pos === 'MIDFIELDER' => 'MF',
                str_starts_with($pos, 'FW') || $pos === 'ATTACCANTE' || $pos === 'FORWARD' => 'FW',
                default => 'MF',
            };
        });

        // Riusa lo stesso partial del voto (il form post a route('formazione.store'))
        return Theme::partial('shortcodes.prossima-partita', [
            'match'         => $match,
            'team'          => $team,
            'formations'    => $formations,
            'playersByRole' => $playersByRole,
        ]);
    }
);

// Pannello admin del shortcode
Shortcode::setAdminConfig('nextmatch-votazione', function (array $attrs) {
    return ShortcodeForm::createFromArray($attrs)
        ->withLazyLoading()
        ->add(
            'team',
            SelectField::class,
            SelectFieldOption::make()
                ->label(__('Squadra'))
                ->choices(['fiorentina' => 'Fiorentina', 'another' => 'Altro'])
                ->defaultValue('fiorentina')
                ->toArray()
        )
        ->add(
            'formations',
            TextField::class,
            TextFieldOption::make()
                ->label(__('Formazioni disponibili (CSV)'))
                ->placeholder('4-3-3,4-2-3-1,3-5-2,4-4-2,5-3-2')
                ->defaultValue('4-3-3,4-2-3-1,3-5-2,4-4-2,5-3-2')
                ->toArray()
        );
});
    


Shortcode::register(
    'formazioni-risultati',
    __('Formazioni – Risultati'),
    __('Mostra i risultati della votazione formazione per una partita'),
    function (ShortcodeCompiler $sc) {
        // 1) resolve match_id priority: shortcode attr > route param > query string
        $matchId = trim((string) ($sc->match_id ?? ''));
        if (!$matchId) {
            $matchId = optional(request()->route())->parameter('match_id') ?: request('match_id');
        }
        if (!$matchId) {
            return null; // nothing to render
        }

        $match = Calendario::where('match_id', $matchId)->first();
        $data  = FormationStats::aggregate($matchId);

        return Theme::partial('shortcodes.formazioni-risultati', compact('match', 'data', 'matchId'));
    }
);

Shortcode::setAdminConfig('formazioni-risultati', function (array $attrs) {
    $choices = ['' => __('(Auto da URL)')];
    Calendario::query()
        ->orderByDesc('match_date')
        ->get()
        ->each(function (Calendario $m) use (&$choices) {
            $home = Arr::get(json_decode($m->home_team, true), 'name', '—');
            $away = Arr::get(json_decode($m->away_team, true), 'name', '—');
            $choices[$m->match_id] = "$home – $away";
        });

    return ShortcodeForm::createFromArray($attrs)
        ->withLazyLoading()
        ->add(
            'match_id',
            SelectField::class,
            SelectFieldOption::make()
                ->label(__('Partita'))
                ->choices($choices)
                ->defaultValue('')
                ->toArray()
        );
});

    // 2️⃣  Register the frontend renderer
    Shortcode::register(
        'diretta-link',                     // ↰ tag name used inside posts
        __('Diretta link'),                 // shortcode title (admin)
        __('Insert a link to a live match'),// shortcode description (admin)
        function (ShortcodeCompiler $sc) {  // what to render
            $matchId = trim($sc->match_id);

            // no ID → nothing to show
            if (!$matchId) {
                return null;
            }

            return Theme::partial('shortcodes.diretta-link', [
                'matchId' => $matchId,
            ]);
        }
    );

    Shortcode::setAdminConfig('diretta-link', function (array $attrs) {

        /*
        |--------------------------------------------------------------
        | Build <value , label> pairs for the <select>
        |--------------------------------------------------------------
        |  • only matches with status = 'Finished'
        |  • value  -> match_id
        |  • label  -> "Fiorentina – Udinese"  (home - away)
        */
        $choices = Calendario::query()
            ->where('status', 'FINISHED')
            ->orderByDesc('match_date')                         // newest first (optional)
            ->get()
            ->mapWithKeys(function (Calendario $m) {
    
                // home_team & away_team come as JSON strings → decode to arrays
                $home = Arr::get(json_decode($m->home_team, true), 'name', '—');
                $away = Arr::get(json_decode($m->away_team, true), 'name', '—');
    
                return [$m->match_id => "$home – $away"];
            })
            ->toArray();
    
        /*
        |--------------------------------------------------------------
        | Return the form (single <select>)
        |--------------------------------------------------------------
        */
        return ShortcodeForm::createFromArray($attrs)
            ->withLazyLoading()
            ->add(
                'match_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Match'))                  // field label in the sidebar
                    ->choices($choices)                   // <option value="match_id">home – away</option>
                    ->defaultValue(array_key_first($choices) ?: null)
                    ->toArray()
            );
    });






    if (is_plugin_active('blog')) {
        Shortcode::setPreviewImage('blog-posts', Theme::asset()->url('images/ui-blocks/blog-posts.png'));

        Shortcode::register(
            'featured-posts',
            __('Featured posts'),
            __('Featured posts'),
            function (ShortcodeCompiler $shortcode) {
                $posts = get_featured_posts((int) $shortcode->limit ?: 5, [
                    'author',
                ]);

                if ($posts->isEmpty()) {
                    return null;
                }

                return Theme::partial('shortcodes.featured-posts', compact('posts', 'shortcode'));
            }
        );


        Shortcode::setAdminConfig('featured-posts', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'limit',
                    NumberField::class,
                    TextFieldOption::make()->label(__('Limit'))->defaultValue(5)->toArray()
                )
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#ecf0f1',
                ]);
        });

        Shortcode::setPreviewImage('featured-posts', Theme::asset()->url('images/ui-blocks/featured-posts.png'));

        Shortcode::register(
            'ads-p1',
            __('Ads P1'),
            __('Ads P1'),
            function (ShortcodeCompiler $shortcode) {


                return Theme::partial('shortcodes.ads-p1');
            }
        );
        Shortcode::register(
            'ads-background',
            __('Ads Background'),
            __('Ads Background'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.adsbackground');
            }
        );

        Shortcode::setPreviewImage('ads-background', Theme::asset()->url('images/ui-blocks/all-galleries.png'));

        Shortcode::setAdminConfig('ads-background', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'limit',
                    NumberField::class,
                    TextFieldOption::make()->label(__('Limit'))->defaultValue(8)->toArray()
                )
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#fff',
                ]);
        });
        Shortcode::register(
            'adsvideo',
            __('Ads video'),
            __('Ads video'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.adsvideo');
            }
        );
        Shortcode::register(
            'adsdiretta',
            __('Ads diretta'),
            __('Ads diretta'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.adsdiretta');
            }
        );

        Shortcode::register(
            'diretta',
            __('Diretta'),
            __('Diretta'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.diretta');
            }
        );

        Shortcode::register(
            'squad',
            __('Squadra'),
            __('Squadra'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.squad');
            }
        );
        Shortcode::register(
            'big classifica',
            __('big classifica'),
            __('big classifica'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.bigclassifica');
            }
        );
        Shortcode::register(
            'bigclassificafm',
            __('big classifica FM'),
            __('big classifica FM'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.bigclassificafm');
            }
        );
        Shortcode::register(
            'bigclassificapv',
            __('big classifica PV'),
            __('big classifica PV'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.bigclassificapv');
            }
        );
        Shortcode::register(
            'calendario',
            __('calendario'),
            __('calendario'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.calendario');
            }
        );
        Shortcode::register(
            'calendarioprimavera',
            __('calendario primavera'),
            __('calendario primavera'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.calendariopv');
            }
        );
        Shortcode::register(
            'calendariofemminile',
            __('calendario femminile'),
            __('calendario femminile'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.calendariofm');
            }
        );

        Shortcode::setPreviewImage('ads-diretta', Theme::asset()->url('images/ui-blocks/all-galleries.png'));

        Shortcode::setAdminConfig('ads-diretta', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'limit',
                    NumberField::class,
                    TextFieldOption::make()->label(__('Limit'))->defaultValue(8)->toArray()
                )
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#fff',
                ]);
        });

        Shortcode::register(
            'main-page',
            __('main page'),
            __('main page'),
            function (ShortcodeCompiler $shortcode) {
                return Theme::partial('shortcodes.main-page');
            }
        );





        Shortcode::register(
            'recent-posts',
            __('Recent posts'),
            __('Recent posts'),
            function (ShortcodeCompiler $shortcode) {
                $posts = get_latest_posts(intval(50), [], ['slugable']);
                $postsCount = get_list_post_count();

                if ($posts->isEmpty()) {
                    return null;
                }

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial('shortcodes.recent-posts', [
                    'title' => $shortcode->title,
                    'withSidebar' => $withSidebar,
                    'posts' => $posts,
                    'postsCount' => $postsCount,
                    'shortcode' => $shortcode,
                ]);
            }
        );

        Shortcode::setPreviewImage('recent-posts', Theme::asset()->url('images/ui-blocks/recent-posts.png'));

        Shortcode::setAdminConfig('recent-posts', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#fff',
                ])
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With top sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                );
        });

        Shortcode::register(
            'featured-categories-posts',
            __('Featured categories posts'),
            __('Featured categories posts'),
            function (ShortcodeCompiler $shortcode) {
                $with = [
                    'slugable',
                    'posts' => function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at');
                    },
                    'posts.slugable',
                ];

                if (is_plugin_active('language-advanced')) {
                    $with[] = 'posts.translations';
                }

                $posts = collect();

                if ($categoryId = $shortcode->category_id) {
                    $with['posts'] = function (BelongsToMany|BaseQueryBuilder $query) {
                        $query
                            ->wherePublished()
                            ->orderByDesc('created_at')
                            ->take(6);
                    };

                    $category = Category::query()
                        ->with($with)
                        ->wherePublished()
                        ->where('id', $categoryId)
                        ->select([
                            'id',
                            'name',
                            'description',
                            'icon',
                        ])
                        ->first();

                    if ($category) {
                        $posts = $category->posts;
                    } else {
                        $posts = collect();
                    }
                } else {
                    $categories = get_featured_categories(2, $with);

                    foreach ($categories as $category) {
                        $posts = $posts->merge($category->posts->take(3));
                    }

                    $posts = $posts->sortByDesc('created_at');
                }

                if ($posts->isEmpty()) {
                    return null;
                }

                $withSidebar = ($shortcode->with_sidebar ?: 'yes') === 'yes';

                return Theme::partial(
                    'shortcodes.featured-categories-posts',
                    [
                        'title' => $shortcode->title,
                        'withSidebar' => $withSidebar,
                        'posts' => $posts,
                        'shortcode' => $shortcode,
                    ]
                );
            }
        );

        Shortcode::setPreviewImage(
            'featured-categories-posts',
            Theme::asset()->url('images/ui-blocks/featured-categories-posts.png')
        );

        Shortcode::setAdminConfig('featured-categories-posts', function (array $attributes) {
            $categories = Category::query()
                ->wherePublished()
                ->select('name', 'id')
                ->get()
                ->mapWithKeys(fn ($item) => [$item->id => $item->name])
                ->all();

            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add('title', TextField::class, TextFieldOption::make()->label(__('Title'))->toArray())
                ->add(
                    'category_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Category'))
                        ->choices(['' => __('All')] + $categories)
                        ->selected(Arr::get($attributes, 'category_id'))
                        ->searchable()
                        ->toArray()
                )
                ->add(
                    'with_sidebar',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('With primary sidebar?'))
                        ->choices(['yes' => __('Yes'), 'no' => __('No')])
                        ->defaultValue('yes')
                        ->toArray()
                )
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#ecf0f1',
                ]);
        });
    }

    if (is_plugin_active('contact')) {
        Shortcode::setPreviewImage('contact-form', Theme::asset()->url('images/ui-blocks/contact-form.png'));
    }

    if (is_plugin_active('gallery')) {
        Shortcode::setPreviewImage('gallery', Theme::asset()->url('images/ui-blocks/gallery.png'));

        Shortcode::register(
            'all-galleries',
            __('All galleries'),
            __('All galleries'),
            function (ShortcodeCompiler $shortcode) {
                if (! function_exists('render_galleries')) {
                    return null;
                }

                $galleries = render_galleries((int) $shortcode->limit ?: 8);

                if (! $galleries) {
                    return null;
                }

                return Theme::partial('shortcodes.all-galleries', compact('galleries', 'shortcode'));
            }
        );

        Shortcode::setPreviewImage('all-galleries', Theme::asset()->url('images/ui-blocks/all-galleries.png'));

        Shortcode::setAdminConfig('all-galleries', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'limit',
                    NumberField::class,
                    TextFieldOption::make()->label(__('Limit'))->defaultValue(8)->toArray()
                )
                ->add('background_color', ColorField::class, [
                    'label' => __('Background color'),
                    'default_value' => '#fff',
                ]);
        });
    }
});
