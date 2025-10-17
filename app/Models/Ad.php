<?php

namespace App\Models;

use App\Support\AdDisplayPool;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use DOMNode;

class Ad extends BaseModel
{
    protected $table = 'ads';

    /** Type */
    const TYPE_ANNUNCIO_IMMAGINE = 1;
    const TYPE_GOOGLE_ADS        = 2;

    const TYPES = [
        self::TYPE_ANNUNCIO_IMMAGINE => 'Annuncio immagine',
        self::TYPE_GOOGLE_ADS        => 'Google Ad Manager',
    ];

    /** Groups (keep your IDs) */
    const GROUP_POPUP_DESKTOP   = 1;
    const GROUP_POPUP_MOBILE    = 2;
    const GROUP_MAIN_PAGE       = 3;
    const GROUP_HERO            = 45;
    const GROUP_BLOG_PAGE       = 4;
    const GROUP_BACKGROUND_PAGE = 5;
    const GROUP_DBLOG_TITLE     = 6;
    const GROUP_DBLOG_AUTHOR    = 7;
    const GROUP_DBLOG_P1        = 8;
    const GROUP_DBLOG_P2        = 9;
    const GROUP_DBLOG_P3        = 10;
    const GROUP_DBLOG_P4        = 11;
    const GROUP_DBLOG_P5        = 12;
    const GROUP_diretta_1       = 13;
    const GROUP_recentp1        = 14;
    const GROUP_recentp2        = 15;
    const GROUP_recentp3        = 16;
    const GROUP_recentp4        = 17;
    const Google_adsense        = 18;
    const SIZE_230X90_DX        = 19;
    const SIZE_230X90_SX        = 20;
    const SIZE_300X250_B1       = 21;
    const SIZE_300X250_C1       = 22;
    const SIZE_300X250_TOP      = 23;
    const SIZE_468X60_TOP_DX    = 24;
    const SIZE_468X60_TOP_SX    = 25;

    const GRUPPO_POPUP_MOBILE   = 31;
    const MOBILE_HOME_TOP_24    = 34;
    const MOBILE_POSIZIONE_1    = 35;
    const MOBILE_POSIZIONE_2    = 36;
    const MOBILE_POSIZIONE_3    = 37;
    const MOBILE_POSIZIONE_4    = 38;
    const MOBILE_POSIZIONE_5    = 39;
    const MOBILE_POSIZIONE_6    = 99;
    const MOBILE_HOME_HERO_25   = 40;
    const MOBILE_DOPO_FOTO_26   = 41;

    const GROUPS = [
        self::GROUP_POPUP_DESKTOP   => 'DESKTOP popup desktop',
        self::GROUP_BACKGROUND_PAGE => 'DESKTOP background page',
        self::GROUP_DBLOG_TITLE     => 'DESKTOP articolo title',
        self::GROUP_HERO            => 'DESKTOP Hero',
        self::GROUP_DBLOG_AUTHOR    => 'DESKTOP articolo author',
        self::GROUP_DBLOG_P1        => 'DESKTOP articolo P1',
        self::GROUP_DBLOG_P2        => 'DESKTOP articolo P2',
        self::GROUP_DBLOG_P3        => 'DESKTOP articolo P3',
        self::GROUP_DBLOG_P4        => 'DESKTOP articolo P4',
        self::GROUP_DBLOG_P5        => 'DESKTOP articolo P5',
        self::GROUP_diretta_1       => 'DESKTOP Diretta_1',
        self::GROUP_recentp1        => 'DESKTOP recentp1',
        self::GROUP_recentp2        => 'DESKTOP recentp2',
        self::GROUP_recentp3        => 'DESKTOP recentp3',
        self::GROUP_recentp4        => 'DESKTOP recentp4',
        self::SIZE_230X90_DX        => 'SIZE_230X90_DX',
        self::SIZE_230X90_SX        => 'SIZE_230X90_SX',
        self::SIZE_300X250_B1       => '320X250 b1 sidebar',
        self::SIZE_300X250_C1       => '320X250 c1 sidebar',
        self::SIZE_300X250_TOP      => '350X250 top sidebar',
        self::SIZE_468X60_TOP_DX    => '468X60 dx',
        self::SIZE_468X60_TOP_SX    => '468X60 sx',
        self::GRUPPO_POPUP_MOBILE   => 'MOBILE_POPUP',
        self::MOBILE_HOME_TOP_24    => 'MOBILE_HOME_Testata',
        self::MOBILE_POSIZIONE_1    => 'MOBILE_POSIZIONE_1',
        self::MOBILE_POSIZIONE_2    => 'MOBILE_POSIZIONE_2',
        self::MOBILE_POSIZIONE_3    => 'MOBILE_POSIZIONE_3',
        self::MOBILE_POSIZIONE_4    => 'MOBILE_POSIZIONE_4',
        self::MOBILE_POSIZIONE_5    => 'MOBILE_POSIZIONE_5',
        self::MOBILE_POSIZIONE_6    => 'MOBILE_POSIZIONE_6',
        self::MOBILE_HOME_HERO_25   => 'MOBILE_HOME_HERO_25',
        self::MOBILE_DOPO_FOTO_26   => 'MOBILE_DOPO_FOTO_26',
    ];

    protected $fillable = [
        'title',
        'type',                 // 1=image, 2=google
        'image',                // legacy single image
        'url',                  // legacy single url
        'urls',                 // optional json array of urls
        'amp',                  // amp code for google type
        'group',                // slot/group constant
        'ad_group_id',          // FK to ad_groups (image banks)
        'starts_at',
        'expires_at',
        'width',
        'height',
        'weight',               // distribution weight (>=1)
        'status',               // published flag (1=on)
        'display_count',        // rotation index
        'visualization_condition',
        'placement',
    ];

    /* ========================= Relations ========================= */

    public function groupRef(): BelongsTo
    {
        return $this->belongsTo(AdGroup::class, 'ad_group_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdImage::class, 'ad_id');
    }

    public function adStatistics(): HasMany
    {
        return $this->hasMany(AdStatistic::class, 'ad_id');
    }

    /* ========================= Scopes ========================= */

    /** Published image-ads */
    public function scopeTypeAnnuncioImmagine($q)
    {
        // Be explicit: type == image AND status == 1 (active)
        return $q->where('type', self::TYPE_ANNUNCIO_IMMAGINE)
                 ->where('status', 1);
    }

    /** Published google ads */
    public function scopeTypeGoogleAds($q)
    {
        return $q->where('type', self::TYPE_GOOGLE_ADS)
                 ->where('status', 1);
    }

    /** Weighted random (avoid divide-by-0) */
    public function scopeInRandomOrderByWeight($q)
    {
        $table = $this->getTable();
        return $q->orderByRaw("-LOG(1 - RAND()) / NULLIF(`{$table}`.`weight`, 0)");
    }

    /* ========================= Helpers ========================= */

    public function getGroupNameAttribute(): string
    {
        return self::GROUPS[$this->group] ?? 'Unknown Group';
    }

    public function hasImage(): bool
    {
        return is_string($this->image) && $this->image !== '';
    }

    public function getEffectiveWeightPerImage(): ?float
    {
        $c = $this->images()->count();
        return $c > 0 ? ($this->weight / $c) : (float) $this->weight;
    }

    /** Legacy: rotate multiple URLs (when urls is JSON array) */
    public function getRedirectUrl(): ?string
    {
        if (! $this->urls) return null;
        $urls = json_decode($this->urls, true);
        if (!is_array($urls) || !$urls) return null;

        $index = ((int) $this->display_count) % count($urls);
        return $urls[$index] ?? null;
    }

    /** Visualization rules */
    protected static function userKey(): string
    {
        return auth()->check() ? ('u' . auth()->id()) : ('s' . session()->getId());
    }

    public function visualization(): ?array
    {
        return $this->visualization_condition
            ? json_decode($this->visualization_condition, true)
            : null;
    }

    public function isEligible(): bool
    {
        $v = $this->visualization();
        if (!$v) return true;

        $key   = static::userKey();
        $cache = "ad:{$this->id}:{$key}";

        if (($v['type'] ?? null) === 'page') {
            return Cache::get("$cache:views", 0) < (int) $v['max'];
        }

        if (($v['type'] ?? null) === 'ad') {
            $views = Cache::get("$cache:views", 0);
            $last  = Cache::get("$cache:last", 0);
            return $views < (int) $v['max'] && (time() - $last) >= (int) $v['seconds'];
        }

        return true;
    }

    public function markShown(): void
    {
        $key   = static::userKey();
        $cache = "ad:{$this->id}:{$key}";
        Cache::increment("$cache:views");
        Cache::put("$cache:last", time(), now()->addDays(30));
    }

    /** Prefer images from the assigned ad_group; fallback to single image */
    public function getDisplayImageUrl(): ?string
    {
        // Only for image-ads
        if ((int) $this->type !== self::TYPE_ANNUNCIO_IMMAGINE) {
            return null;
        }

        // 1) via ad_group images (bank)
        $group = $this->groupRef()->with('images')->first();
        if ($group && $group->images->count() > 0) {
            $count = $group->images->count();
            $idx   = ((int) ($this->display_count ?? 0)) % $count;
            $key   = $group->images[$idx]->image_url ?? null;

            if (is_string($key) && $key !== '') {
                return preg_match('~^https?://~i', $key)
                    ? $key
                    : Storage::disk('wasabi')->url(ltrim($key, '/'));
            }
        }

        // 2) legacy single image
        if ($this->hasImage()) {
            return preg_match('~^https?://~i', $this->image)
                ? $this->image
                : Storage::disk('wasabi')->url(ltrim($this->image, '/'));
        }

        return null;
    }

    /** Kept for BC with your template accessor */
    public function getOptimizedImageUrlAttribute(): ?string
    {
        return $this->getDisplayImageUrl();
    }

    /* ========================= Content Injection (Articles) =========================
       Uses AdDisplayPool for P1..P5 to avoid duplicates and respect weight.
       You already split content in blocks before calling this.
    ================================================================================*/

    public static function addAdsToContent($content)
    {
        // collect all groups used in article content
        $whereGroups = [
            self::GROUP_BACKGROUND_PAGE,
            self::GROUP_DBLOG_P1,
            self::GROUP_DBLOG_P2,
            self::GROUP_DBLOG_P3,
            self::GROUP_DBLOG_P4,
            self::GROUP_DBLOG_P5,

            // mobile (keep your references if needed)
            self::MOBILE_POSIZIONE_1,
            self::MOBILE_POSIZIONE_2,
            self::MOBILE_POSIZIONE_4,
            self::MOBILE_POSIZIONE_5,
            self::MOBILE_POSIZIONE_6,
        ];

        $ads = self::query()
            ->typeAnnuncioImmagine()
            ->whereIn('group', $whereGroups)
            ->get()
            ->unique('group')
            ->mapWithKeys(fn ($a) => [$a->group => $a]);

        // Background (skin)
        if ($ads->has(self::GROUP_BACKGROUND_PAGE)) {
            $bgHtml = view('ads.includes.background-page', [
                'ad' => $ads[self::GROUP_BACKGROUND_PAGE],
            ])->render();

            $content = preg_replace(
                '/(<section[^>]*class="section[^"]*"[^>]*>)/i',
                $bgHtml . '$1',
                $content,
                1
            );
        }

        // split into atomic blocks (shortcode | p | h1-6)
        preg_match_all(
            '/<shortcode>[\s\S]*?<\/shortcode>'
            . '|<p[^>]*?>[\s\S]*?<\/p>'
            . '|<h[1-6][^>]*?>[\s\S]*?<\/h[1-6]>/i',
            $content,
            $m
        );

        if (empty($m[0])) {
            return $content;
        }

        $blocks    = collect($m[0]);
        $paraIndex = 0;
        $out       = [];

        // ===== Desktop unique allocation for P1..P5 via AdDisplayPool =====
        $desktopSlots = [
            self::GROUP_DBLOG_P1,
            self::GROUP_DBLOG_P2,
            self::GROUP_DBLOG_P3,
            self::GROUP_DBLOG_P4,
            self::GROUP_DBLOG_P5,
        ];

        // collect ad_group_ids from the selected ads
        $adGroupIds = [];
        foreach ($desktopSlots as $slotConst) {
            $ad = $ads[$slotConst] ?? null;
            if ($ad && !empty($ad->ad_group_id)) {
                $adGroupIds[] = (int) $ad->ad_group_id;
            }
        }
        $adGroupIds = array_values(array_unique($adGroupIds));

        /** @var AdDisplayPool $pool */
        $pool = app(AdDisplayPool::class);
        if ($adGroupIds) {
            $pool->allocateUnique($adGroupIds);
        }

        // helper to render a pre-allocated image for a slot; fallback to legacy partial
        $renderDesktopAd = function (int $slotConst) use ($ads, $pool) {
            $ad  = $ads[$slotConst] ?? null;
            $gid = $ad->ad_group_id ?? null;

            // Fallback view mapping
            $fallbackView = [
                self::GROUP_DBLOG_P1 => 'ads.includes.dblog-p1',
                self::GROUP_DBLOG_P2 => 'ads.includes.dblog-p2',
                self::GROUP_DBLOG_P3 => 'ads.includes.dblog-p3',
                self::GROUP_DBLOG_P4 => 'ads.includes.dblog-p4',
                self::GROUP_DBLOG_P5 => 'ads.includes.dblog-p5',
            ];

            $fallback = function () use ($slotConst, $ads, $fallbackView) {
                $v = $fallbackView[$slotConst] ?? null;
                return ($v && isset($ads[$slotConst]))
                    ? view($v, ['ad' => $ads[$slotConst]])->render()
                    : '';
            };

            if (!$gid) {
                return $fallback();
            }

            $img = $pool->getAllocated($gid); // \App\Models\AdGroupImage|null
            if (!$img) {
                return $fallback();
            }

            $src = preg_match('~^https?://~i', $img->image_url)
                ? $img->image_url
                : Storage::disk('wasabi')->url(ltrim($img->image_url, '/'));

            $href = $img->target_url ?: '#';

            return '<div class="ads-slot" style="text-align:center;margin:12px 0;">'
                . '<a href="' . e($href) . '" target="_blank" rel="nofollow sponsored noopener">'
                . '<img src="' . e($src) . '" alt="sponsored" style="max-width:100%;height:auto;">'
                . '</a>'
                . '</div>';
        };

        foreach ($blocks as $block) {
            $out[] = $block;

            if (preg_match('/^<p/i', $block)) {
                $paraIndex++;

                switch ($paraIndex) {
                    case 1:
                        $out[] = $renderDesktopAd(self::GROUP_DBLOG_P1);
                        if ($ads->has(self::MOBILE_POSIZIONE_1)) {
                            $out[] = view('ads.includes.MOBILE_POSIZIONE_1', [
                                'ad' => $ads[self::MOBILE_POSIZIONE_1],
                            ])->render();
                        }
                        break;

                    case 2:
                        $out[] = $renderDesktopAd(self::GROUP_DBLOG_P2);
                        if ($ads->has(self::MOBILE_POSIZIONE_2)) {
                            $out[] = view('ads.includes.MOBILE_POSIZIONE_2', [
                                'ad' => $ads[self::MOBILE_POSIZIONE_2],
                            ])->render();
                        }
                        break;

                    case 3:
                        $out[] = $renderDesktopAd(self::GROUP_DBLOG_P3);
                        if ($ads->has(self::MOBILE_POSIZIONE_5)) {
                            $out[] = view('ads.includes.MOBILE_POSIZIONE_3', [
                                'ad' => $ads[self::MOBILE_POSIZIONE_5],
                            ])->render();
                        }
                        break;

                    case 4:
                        $out[] = $renderDesktopAd(self::GROUP_DBLOG_P4);
                        if ($ads->has(self::MOBILE_POSIZIONE_4)) {
                            $out[] = view('ads.includes.MOBILE_POSIZIONE_4', [
                                'ad' => $ads[self::MOBILE_POSIZIONE_4],
                            ])->render();
                        }
                        break;

                    default:
                        $out[] = $renderDesktopAd(self::GROUP_DBLOG_P5);
                        if ($ads->has(self::MOBILE_POSIZIONE_5)) {
                            $out[] = view('ads.includes.MOBILE_POSIZIONE_3', [
                                'ad' => $ads[self::MOBILE_POSIZIONE_5],
                            ])->render();
                        }
                }
            }
        }

        return implode('', $out);
    }

    /* ========================= HTML utils kept as-is ========================= */

    public static function wrapInlineIntoParagraphs(string $html): string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML('<meta charset="utf-8"><div id="root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xp   = new DOMXPath($doc);
        $root = $xp->query('//*[@id="root"]')->item(0);
        if (!$root) return $html;

        $isBlock = static function (DOMNode $n): bool {
            if ($n->nodeType !== XML_ELEMENT_NODE) return false;
            $name = strtoupper($n->nodeName);
            return in_array($name, ['P','H1','H2','H3','H4','H5','H6','SHORTCODE','UL','OL','TABLE','BLOCKQUOTE'], true);
        };

        // Rebuild children, buffering inline/text into <p>
        $children = iterator_to_array($root->childNodes);
        foreach ($children as $n) $root->removeChild($n);
        $buffer = [];
        $flush  = function () use (&$buffer, $doc, $root) {
            if (!$buffer) return;
            $p = $doc->createElement('p');
            foreach ($buffer as $n) $p->appendChild($n);
            $root->appendChild($p);
            $buffer = [];
        };

        foreach ($children as $n) {
            if ($n->nodeType === XML_TEXT_NODE && trim($n->nodeValue) === '') continue;
            if ($isBlock($n)) { $flush(); $root->appendChild($n); }
            else             { $buffer[] = $n; }
        }
        $flush();

        $out = '';
        foreach ($root->childNodes as $n) $out .= $doc->saveHTML($n);
        libxml_clear_errors();
        return $out;
    }

    public static function normalizeContent($html): string
    {
        if (!mb_detect_encoding($html, 'UTF-8', true)) {
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<!DOCTYPE html><meta charset="utf-8"><div id="__root__">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $root  = $xpath->query('//*[@id="__root__"]')->item(0);
        if (!$root) return $html;

        foreach ($xpath->query('//*[@style]') as $el) {
            $el->removeAttribute('style');
        }
        foreach ($xpath->query('//p') as $p) {
            $text = trim($p->textContent ?? '');
            if ($text === '' && $p->childNodes->length === 0) {
                $p->parentNode->removeChild($p);
            }
        }

        $isBlock = function (DOMNode $n): bool {
            if ($n->nodeType !== XML_ELEMENT_NODE) return false;
            return in_array(strtoupper($n->nodeName), ['P','H1','H2','H3','H4','H5','H6','SHORTCODE'], true);
        };
        $isIgnorableText = function (DOMNode $n): bool {
            return $n->nodeType === XML_TEXT_NODE && trim($n->nodeValue) === '';
        };

        $children = [];
        foreach (iterator_to_array($root->childNodes) as $node) {
            $children[] = $node;
        }

        $buffer = [];
        $flushBufferAsParagraph = function () use (&$buffer, $doc, $root) {
            if (empty($buffer)) return;
            $p = $doc->createElement('p');
            foreach ($buffer as $n) $p->appendChild($n);
            $buffer = [];
            $root->appendChild($p);
        };

        foreach ($children as $n) {
            $root->removeChild($n);
        }

        foreach ($children as $n) {
            if ($isIgnorableText($n)) continue;

            if ($isBlock($n)) {
                $flushBufferAsParagraph();
                $root->appendChild($n);
            } else {
                $buffer[] = $n;
            }
        }
        $flushBufferAsParagraph();

        foreach ($xpath->query('//*[@id="__root__"]//p') as $p) {
            $text = trim($p->textContent ?? '');
            if ($text === '' && $p->childNodes->length === 0) {
                $p->parentNode->removeChild($p);
            }
        }

        $out = '';
        foreach ($root->childNodes as $n) $out .= $doc->saveHTML($n);
        return $out;
    }

    public static function splitLongParagraphs(string $html, int $rows = 5, int $rowWidth = 95): string
    {
        $html = self::wrapInlineIntoParagraphs($html);
        $threshold = $rows * $rowWidth;

        return preg_replace_callback('/<p[^>]*>(.*?)<\/p>/is', function ($m) use ($threshold) {
            $inner = $m[1];
            $plain = trim(strip_tags($inner));
            if (mb_strlen($plain) <= $threshold) {
                return '<p>' . $inner . '</p>';
            }

            $parts = preg_split(
                '/(?<=[.!?])(?:\s|<\/(?:strong|em|b|i|span|a|u|small|sup|sub|mark|code)[^>]*>)+/iu',
                $inner,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $chunks = [];
            $buf = '';
            foreach ($parts as $s) {
                $buf .= $s . ' ';
                if (mb_strlen(trim(strip_tags($buf))) >= $threshold) {
                    $chunks[] = trim($buf);
                    $buf = '';
                }
            }
            if (trim($buf) !== '') $chunks[] = trim($buf);

            $out = '';
            foreach ($chunks as $c) $out .= '<p>' . $c . '</p>';
            return $out;
        }, $html);
    }

    public static function paragraphsEveryRows(string $html, int $rows = 5, int $rowWidth = 95): string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML('<meta charset="utf-8"><div id="root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xp   = new DOMXPath($doc);
        $root = $xp->query('//*[@id="root"]')->item(0);
        if (!$root) return $html;

        $isBlock = static function (DOMNode $n): bool {
            if ($n->nodeType !== XML_ELEMENT_NODE) return false;
            return in_array(strtoupper($n->nodeName), ['H1','H2','H3','H4','H5','H6','SHORTCODE'], true);
        };

        $threshold = $rows * $rowWidth;

        $flushInlineToParagraphs = static function (string $inlineHtml, int $threshold) : string {
            $inlineHtml = trim($inlineHtml);
            if ($inlineHtml === '' || mb_strlen(trim(strip_tags($inlineHtml))) === 0) return '';

            $sentences = preg_split(
                '/(?<=[.!?])(?:\s|<\/(?:strong|em|b|i|span|a|u|small|sup|sub|mark|code)[^>]*>)+/iu',
                $inlineHtml,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $out = '';
            $buf = '';
            foreach ($sentences as $s) {
                $buf .= $s . ' ';
                if (mb_strlen(trim(strip_tags($buf))) >= $threshold) {
                    $out .= '<p>' . trim($buf) . '</p>';
                    $buf = '';
                }
            }
            if (trim(strip_tags($buf)) !== '') {
                $out .= '<p>' . trim($buf) . '</p>';
            }
            return $out;
        };

        $out = '';
        $inline = '';

        foreach (iterator_to_array($root->childNodes) as $node) {
            if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) === '') continue;

            if ($isBlock($node)) {
                $out .= $flushInlineToParagraphs($inline, $threshold);
                $inline = '';
                $out .= $doc->saveHTML($node);
            } else {
                $inline .= $doc->saveHTML($node);
            }
        }

        $out .= $flushInlineToParagraphs($inline, $threshold);

        return $out;
    }
}
