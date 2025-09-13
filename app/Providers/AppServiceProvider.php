<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Video;
use Botble\Blog\Models\Category;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\MailManager;
use App\Mail\Transport\SendGridTransport;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\HttpClient\HttpClient;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
// Force HTMLPurifier cache to a writable path (works even if config is cached)
        $purifierPath = env('PURIFIER_CACHE_PATH', storage_path('app/purifier'));

        // Best effort: create the directory if missing
        if (!is_dir($purifierPath)) {
            @mkdir($purifierPath, 0777, true);
        }
        @chmod($purifierPath, 0777);

        // Set both the package cachePath and the HTMLPurifier setting used by some versions
        config()->set('purifier.cachePath', $purifierPath);

        foreach (['default', 'general', 'youtube'] as $group) {
            // This key is honored by HTMLPurifier itself
            config()->set("purifier.settings.$group.Cache.SerializerPath", $purifierPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MailManager $manager): void
    {
        URL::forceScheme('https');

        set_time_limit(900); // Sets the maximum execution time to 15 minutes

        view()->composer('ads.includes.main-page', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_MAIN_PAGE)->get());
        });
        view()->composer('ads.includes.blog-page', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_BLOG_PAGE)->get());
        });


        view()->composer('ads.includes.dblog-author', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_AUTHOR)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });
        view()->composer('ads.includes.dblog-title', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_TITLE)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });


        view()->composer('ads.includes.dblog-p1', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_P1)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });

        view()->composer('ads.includes.dblog-p2', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_P2)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });

        view()->composer('ads.includes.dblog-p3', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_P3)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });

        view()->composer('ads.includes.dblog-p4', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_P4)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });

        view()->composer('ads.includes.dblog-p5', function ($view) {
            $ad = Ad::query()
                ->typeAnnuncioImmagine()
                ->where('group', Ad::GROUP_DBLOG_P5)
                ->inRandomOrderByWeight()
                ->first();
    
            $view->with('ad', $ad);
        });

        view()->composer('ads.includes.background-page', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_BACKGROUND_PAGE)->inRandomOrderByWeight()->first());
        });
              view()->composer('ads.includes.adsdiretta', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_diretta_1)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.adsrecentp1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp1)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.adsrecentp2', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp2)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.adsrecentp3', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp3)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.adsrecentp4', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp4)->inRandomOrderByWeight()->first());
        });
                view()->composer('ads.includes.adsHero', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_HERO)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.adsense', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::Google_adsense)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.SIZE_468X60_TOP_SX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_468X60_TOP_SX)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.SIZE_468X60_TOP_DX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_468X60_TOP_DX)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.SIZE_300X250_TOP', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_300X250_TOP)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.SIZE_300X250_C1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_300X250_C1)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.SIZE_300X250_B1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_300X250_C1)->inRandomOrderByWeight()->first());
        });

        view()->composer('ads.includes.SIZE_230X90_DX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_230X90_DX)->inRandomOrderByWeight()->first());
        });

        view()->composer('ads.includes.MOBILE_HOME_TOP_24', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_HOME_TOP_24)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_HOME_HERO_25', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_HOME_HERO_25)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_DOPO_FOTO_26', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_DOPO_FOTO_26)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_POSIZIONE_1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_1)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_POSIZIONE_2', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_2)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_POSIZIONE_3', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_3)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_POSIZIONE_4', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_4)->inRandomOrderByWeight()->first());
        });
        view()->composer('ads.includes.MOBILE_POSIZIONE_5', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_5)->inRandomOrderByWeight()->first());
        });
                view()->composer('ads.includes.MOBILE_POSIZIONE_6', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_POSIZIONE_6)->inRandomOrderByWeight()->first());
        });

        view()->composer('videos.includes.adsvideo', function (View $view) {
            $is_home = request()->route()->getName() == "public.index";
            $video = Video::query()
                ->when($is_home, function ($q) {
                    $q->onlyForHome();
                }, function ($q) {
                    $q->onlyForPost();
                })
                ->published()
                ->first();
            if ($video) {
                $mediaFiles = $video->mediaFiles()
                    ->when($video->isRandom(), function ($q) {
                        $q->inRandomOrder();
                    }, function ($q) {
                        $q->orderBy('priority');
                    })
                    ->get();
                $video_files = $mediaFiles->map(function ($item) {
                    return \Illuminate\Support\Facades\Storage::disk("laviolas3")->url($item->url);
                });
                $video_file_urls = $mediaFiles->map(function ($item) {
                    return $item->pivot->url;
                });;
            } else {
                $video_files = collect();
                $video_file_urls = collect();
            }
            $view->with('video', $video)->with('video_files', $video_files)->with('video_file_urls',$video_file_urls);
        });

        view()->composer('last_post_editoriale', function (View $view) {
            $category = Category::query()->find(157);
            $last_post = $category?->posts()->latest()->limit(1)->first();
            $view->with('last_post',$last_post);
        });


        // Retrieve the SendGrid API key from the database
        $sendgridApiKey = env("MAIL_PASSWORD");

    // Override the mail configuration for SMTP
    Config::set('mail.mailers.smtp.username', 'apikey');  
    Config::set('mail.mailers.smtp.password', $sendgridApiKey);
    putenv("MAIL_PASSWORD=".$sendgridApiKey);

    $manager->extend('sendgrid', function () {
        // Retrieve your API key from config or DB
        $apiKey = config('mail.mailers.smtp.password'); // Make sure this is set
        // Create an HTTP client instance (or inject one via the service container)
        $client = HttpClient::create();
        return new SendGridTransport($apiKey, $client);
    });
    $this->app->booted(function () {
        config()->set('mail.default', "sendgrid");
    });

    }
}
