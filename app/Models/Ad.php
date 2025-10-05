<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use DOMNode;


class Ad extends BaseModel
{
    protected $table = 'ads';

    const TYPE_ANNUNCIO_IMMAGINE = 1;
    const TYPE_GOOGLE_ADS = 2;

    const TYPES = [
        self::TYPE_ANNUNCIO_IMMAGINE => "Annuncio immagine",
        self::TYPE_GOOGLE_ADS => "Google Ad Manager",
    ];

    const GROUP_POPUP_DESKTOP = 1;
    const GROUP_POPUP_MOBILE = 2;
    const GROUP_MAIN_PAGE = 3;
    const GROUP_HERO = 45;
    const GROUP_BLOG_PAGE = 4;
    const GROUP_BACKGROUND_PAGE = 5;
    const GROUP_DBLOG_TITLE = 6;
    const GROUP_DBLOG_AUTHOR = 7;
    const GROUP_DBLOG_P1 = 8;
    const GROUP_DBLOG_P2 = 9;
    const GROUP_DBLOG_P3 = 10;
    const GROUP_DBLOG_P4 = 11;
    const GROUP_DBLOG_P5 = 12;
    const GROUP_diretta_1 = 13;
    const GROUP_recentp1 = 14;
    const GROUP_recentp2 = 15;
    const GROUP_recentp3 = 16;
    const GROUP_recentp4 = 17;
    const Google_adsense = 18;
    const SIZE_230X90_DX = 19;
    const SIZE_230X90_SX = 20;
    const SIZE_300X250_B1 = 21;
    const SIZE_300X250_C1 = 22;
    const SIZE_300X250_TOP = 23;
    const SIZE_468X60_TOP_DX = 24;
    const SIZE_468X60_TOP_SX = 25;
    // const SIZE_728X90_B1 = 26;
    // const SIZE_728X90_C1 = 27;
    // const SIZE_728X90_C2 = 28;
    // const SIZE_728X90_TESTATA = 29;

    // const GRUPPO_POPUP_DESKTOP = 30;
    const GRUPPO_POPUP_MOBILE = 31;
    // const IN_ARTICLE_DESKTOP_2024 = 32;
    const MOBILE_HOME_TOP_24 = 34;
    const MOBILE_POSIZIONE_1 = 35;
    const MOBILE_POSIZIONE_2 = 36;
    const MOBILE_POSIZIONE_3 = 37;
    const MOBILE_POSIZIONE_4 = 38;
    const MOBILE_POSIZIONE_5 = 39;
    const MOBILE_POSIZIONE_6 = 99;
    const MOBILE_HOME_HERO_25 = 40;
    const MOBILE_DOPO_FOTO_26 = 41;
    const SKIN_MOBILE = 43;


    const GROUPS = [
        self::GROUP_POPUP_DESKTOP => "DESKTOP popup desktop",

        // self::GROUP_MAIN_PAGE => "DESKTOP main page",
        // self::GROUP_BLOG_PAGE => "DESKTOP blog page",
        self::GROUP_BACKGROUND_PAGE => "DESKTOP background page",
        self::GROUP_DBLOG_TITLE => "DESKTOP articolo title",
        self::GROUP_HERO => "DESKTOP Hero",
        self::GROUP_DBLOG_AUTHOR => "DESKTOP articolo author",
        self::GROUP_DBLOG_P1 => "DESKTOP articolo P1",
        self::GROUP_DBLOG_P2 => "DESKTOP articolo P2",
        self::GROUP_DBLOG_P3 => "DESKTOP articolo P3",
        self::GROUP_DBLOG_P4 => "DESKTOP  articolo P4",
        self::GROUP_DBLOG_P5 => "DESKTOP articolo P5",
        self::GROUP_diretta_1 => "DESKTOP Diretta_1",
        self::GROUP_recentp1 => "DESKTOP recentp1",
        self::GROUP_recentp2 => "DESKTOP recentp2",
        self::GROUP_recentp3 => "DESKTOP recentp3",
        self::GROUP_recentp4 => "DESKTOP recentp4",
        // self::Google_adsense => "Google n1",
        self::SIZE_230X90_DX => "SIZE_230X90_DX",
        self::SIZE_230X90_SX => "SIZE_230X90_SX",
        self::SIZE_300X250_B1 => "320X250 b1 sidebar",
        self::SIZE_300X250_C1 => "320X250 c1 sidebar",
        self::SIZE_300X250_TOP => "350X250 top sidebar",
        self::SIZE_468X60_TOP_DX => "468X60 dx",
        self::SIZE_468X60_TOP_SX => "468X60 sx",
        self::GRUPPO_POPUP_MOBILE => "MOBILE_POPUP",
        self::MOBILE_HOME_TOP_24 => "MOBILE_HOME_Testata",
        self::MOBILE_POSIZIONE_1 => "MOBILE_POSIZIONE_1",
        self::MOBILE_POSIZIONE_2 => "MOBILE_POSIZIONE_2",
        self::MOBILE_POSIZIONE_3 => "MOBILE_POSIZIONE_3",
        self::MOBILE_POSIZIONE_4 => "MOBILE_POSIZIONE_4",
        self::MOBILE_POSIZIONE_5 => "MOBILE_POSIZIONE_5",
        self::MOBILE_POSIZIONE_6 => "MOBILE_POSIZIONE_6",
        // self::SKIN_MOBILE => "SKIN_MOBILE",
        self::MOBILE_HOME_HERO_25 => "MOBILE_HOME_HERO_25",
        self::MOBILE_DOPO_FOTO_26 => "MOBILE_DOPO_FOTO_26",
        /*self::SIZE_728X90_B1 => "SIZE_468X60_TOP_SX",
        self::SIZE_728X90_C1 => "SIZE_468X60_TOP_SX",
        self::SIZE_728X90_C2 => "SIZE_468X60_TOP_SX",
        self::SIZE_728X90_TESTATA => "SIZE_468X60_TOP_SX",
        self::GRUPPO_POPUP_DESKTOP => "SIZE_468X60_TOP_SX",
        self::IN_ARTICLE_DESKTOP_2024 => "SIZE_468X60_TOP_SX",*/
    ];

    protected $fillable = [
        'title',
        'type',
        'image',
        'url',
        'amp',
        'group',
        'starts_at',
        'expires_at',
        'width',
        'height',
        'weight',
        'status',
            'visualization_condition',   //  ← NEW
        'placement', 
    ];

    /**
     * @return int
     */
    public function getWeightPercentage(): int
    {
        $sumWeight = self::query()->where('group',$this->group)->sum('weight');
        return intval((100 / $sumWeight) * $this->weight);
    }


    #
        protected static function userKey(): string
    {
        return auth()->check()
            ? 'u' . auth()->id()           // logged-in user
            : 's' . session()->getId();    // anonymous per-session
    }

    // ------------------------------------------------------------------
    //  Eligibility & accounting
    // ------------------------------------------------------------------
    public function isEligible(): bool
    {
        $v = $this->visualization();       // ← your JSON column
        if (!$v) return true;              // no limits

        $key = static::userKey();
        $cache = "ad:{$this->id}:{$key}";

        if ($v['type'] === 'page') {
            return Cache::get("$cache:views", 0) < $v['max'];
        }

        if ($v['type'] === 'ad') {
            $views = Cache::get("$cache:views", 0);
            $last  = Cache::get("$cache:last", 0);
            return $views < $v['max'] && (time() - $last) >= $v['seconds'];
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

    public function visualization(): ?array
    {
        return $this->visualization_condition
            ? json_decode($this->visualization_condition, true)
            : null;
    }

    public function getGroupNameAttribute()
    {
        return self::GROUPS[$this->group] ?? 'Unknown Group';
    }

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * @return string
     */
    // in App\Models\Ad
public function groupRef() { return $this->belongsTo(\App\Models\AdGroup::class, 'ad_group_id'); }

/** Use group images for display */
public function getImageUrl()
{
    if ((int)$this->type === self::TYPE_ANNUNCIO_IMMAGINE) {
        $g = $this->groupRef()->with('images')->first();
        if ($g && $g->images->count()) {
            $idx = $this->display_count % max(1, $g->images->count());
            $key = $g->images[$idx]->image_url ?? null;
            if ($key) {
                return preg_match('~^https?://~i', $key) ? $key : \Storage::disk('wasabi')->url($key);
            }
        }
        // legacy fallback (single image on ad)
        if ($this->image) {
            return preg_match('~^https?://~i', $this->image)
                ? $this->image
                : \Storage::disk('wasabi')->url($this->image);
        }
        return null;
    }
    return $this->image; // for Google/AMP ads, it's code or external
}

public function getOptimizedImageUrlAttribute()
{
    return $this->getImageUrl(); // keep simple; your GIF -optimized logic can be added back
}

        



//     public function getOptimizedImageUrlAttribute()
// {
//     $url = $this->getImageUrl();
// return $url;
//     // $path = parse_url($url, PHP_URL_PATH); // "/ads-images/TdxJ32Y4H4rSpfRdthpL53GVvN7EqhW11732631979.gif"
//     // if (stripos($path, '.gif') !== false) {
//     //         $fileKey = ltrim($path, '/'); // e.g. "ads-images/abc.gif" OR a long CDN path
//     // $dir = pathinfo($fileKey, PATHINFO_DIRNAME);
//     // $filenameWithoutExt = pathinfo($fileKey, PATHINFO_FILENAME);

//     // if (!$dir || !$filenameWithoutExt) {
//     //     return $url; // safety fallback
//     // }

//     // $optimizedKey = $dir . '/' . $filenameWithoutExt . '-optimized.gif';

//     // // If original was absolute URL (CDN), still try Wasabi key (common in your flow).
//     // return Storage::disk('wasabi')->url($optimizedKey);
//     // }else{
        
//     // }        // Remove the leading slash to get the storage key


// }




    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        return !is_null($this->image) && strlen($this->image);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeTypeAnnuncioImmagine($query): mixed
    {
        return $query->where('status', self::TYPE_ANNUNCIO_IMMAGINE);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeTypeGoogleAds($query): mixed
    {
        return $query->where('status', self::TYPE_GOOGLE_ADS);
    }


    public function getRedirectUrl(): ?string
{
    if (!$this->urls) return null;

    $urls = json_decode($this->urls, true);
    $count = count($urls);

    if ($count === 0) return null;

    // uses the same $index logic you already have
    $index = $this->display_count % $count;
    return $urls[$index] ?? null;
}

    public static function addAdsToContent($content)
    {
        /* ─────────────────────────────────────────────────────────────
         * 0.  Collect all ads we may need
         * ───────────────────────────────────────────────────────────*/
        $whereGroups = [
            self::GROUP_BACKGROUND_PAGE,
            self::GROUP_DBLOG_P1,
            self::GROUP_DBLOG_P2,
            self::GROUP_DBLOG_P3,
            self::GROUP_DBLOG_P4,
            self::GROUP_DBLOG_P5,
        
            // add the mobile slots you reference later
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
                ->mapWithKeys(fn($a) => [$a->group => $a]);
        /* ─────────────────────────────────────────────────────────────
         * 1.  Inject the background ad (desktop skin)
         * ───────────────────────────────────────────────────────────*/
        if ($ads->has(self::GROUP_BACKGROUND_PAGE)) {
            $bgHtml  = view('ads.includes.background-page',
                            ['ad' => $ads[self::GROUP_BACKGROUND_PAGE]])->render();
    
            // prepend right before the <section class="section …"> wrapper
            $content = preg_replace(
                '/(<section[^>]*class="section[^"]*"[^>]*>)/i',
                $bgHtml . '$1',
                $content,
                1
            );
        }
    
        /* ─────────────────────────────────────────────────────────────
 * 1.  Split the markup into atomic blocks
 *     (shortcode | p | h1-6)
 * ───────────────────────────────────────────────────────────*/
preg_match_all(
    '/<shortcode>[\s\S]*?<\/shortcode>'
  . '|<p[^>]*?>[\s\S]*?<\/p>'
  . '|<h[1-6][^>]*?>[\s\S]*?<\/h[1-6]>/i',
    $content,
    $m
);

if (empty($m[0])) {
    return $content;                 // nothing matched
}

$blocks = collect($m[0]);

/* ─────────────────────────────────────────────────────────────
 * 2.  Walk through the blocks and drop ads after paragraph #n
 * ───────────────────────────────────────────────────────────*/
$paraIndex = 0;
$out       = [];

foreach ($blocks as $block) {
    $out[] = $block;

    // count only real paragraphs
    if (preg_match('/^<p/i', $block)) {
        $paraIndex++;

        switch ($paraIndex) {
            case 1:
                if ($ads->has(self::GROUP_DBLOG_P1)) {
                    $out[] = view('ads.includes.dblog-p1',
                                  ['ad' => $ads[self::GROUP_DBLOG_P1]])->render();
                    $out[] = view('ads.includes.MOBILE_POSIZIONE_1',
                                  ['ad' => $ads[self::MOBILE_POSIZIONE_1]])->render();
                }
                break;

            case 2:
                if ($ads->has(self::GROUP_DBLOG_P2)) {
                    $out[] = view('ads.includes.dblog-p2',
                                  ['ad' => $ads[self::GROUP_DBLOG_P2]])->render();
                    $out[] = view('ads.includes.MOBILE_POSIZIONE_2',
                                  ['ad' => $ads[self::MOBILE_POSIZIONE_2]])->render();
                }
                break;

            case 3:
                if ($ads->has(self::GROUP_DBLOG_P3)) {
                    $out[] = view('ads.includes.dblog-p3',
                                  ['ad' => $ads[self::GROUP_DBLOG_P3]])->render();
                    $out[] = view('ads.includes.MOBILE_POSIZIONE_3',
                                  ['ad' => $ads[self::MOBILE_POSIZIONE_5]])->render();
                }
                break;

            case 4:
                if ($ads->has(self::GROUP_DBLOG_P4)) {
                    $out[] = view('ads.includes.dblog-p4',
                                  ['ad' => $ads[self::GROUP_DBLOG_P4]])->render();
                    $out[] = view('ads.includes.MOBILE_POSIZIONE_4',
                                  ['ad' => $ads[self::MOBILE_POSIZIONE_4]])->render();
                }
                break;

            default:        // paragraph 5+
                if ($ads->has(self::GROUP_DBLOG_P5)) {
                    $out[] = view('ads.includes.dblog-p5',
                                  ['ad' => $ads[self::GROUP_DBLOG_P5]])->render();
                                                      $out[] = view('ads.includes.MOBILE_POSIZIONE_3',
                                  ['ad' => $ads[self::MOBILE_POSIZIONE_5]])->render();
                }
        }
    }
}

/* ─────────────────────────────────────────────────────────────
 * 3.  Stitch everything back together
 * ───────────────────────────────────────────────────────────*/
return implode('', $out);
    
        /* ─────────────────────────────────────────────────────────────
         * 5.  Preserve [ads‑background] shortcode if present
         * ───────────────────────────────────────────────────────────*/
        $bgShortcodeRegex = '/<shortcode>\[ads-background.*?\].*?\[\/ads-background.*?\]<\/shortcode>/';
    
        if ($shortCodes->count()) {
            $bgCode = $shortCodes->first(fn ($s) => preg_match($bgShortcodeRegex, $s));
            if ($bgCode) {
                Theme::set('has-ads-background', $bgCode);
                $shortCodes = $shortCodes->reject(fn ($s) => preg_match($bgShortcodeRegex, $s));
            }
        }
    
        /* ─────────────────────────────────────────────────────────────
         * 6.  Return merged:   shortcodes  +  content/chunks/ads
         * ───────────────────────────────────────────────────────────*/
        return $shortCodes->merge($assembled)->implode('');
    }
 

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
    // Safety: ensure UTF-8
    if (!mb_detect_encoding($html, 'UTF-8', true)) {
        $html = mb_convert_encoding($html, 'UTF-8', 'auto');
    }

    // Load as a fragment inside a known root
    $doc = new DOMDocument('1.0', 'UTF-8');
    // Suppress warnings from malformed HTML
    libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<!DOCTYPE html><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><div id="__root__">'.$html.'</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath = new DOMXPath($doc);
    $root  = $xpath->query('//*[@id="__root__"]')->item(0);

    if (!$root) {
        return $html;
    }

    // 1) Strip all style attributes
    foreach ($xpath->query('//*[@style]') as $el) {
        $el->removeAttribute('style');
    }

    // 2) Remove empty <p> tags (no text or only whitespace & no element children with text)
    foreach ($xpath->query('//p') as $p) {
        $text = trim($p->textContent ?? '');
        if ($text === '' && $p->childNodes->length === 0) {
            $p->parentNode->removeChild($p);
        }
    }

    // Helper: decide if a node should be treated as a "block boundary"
    $isBlock = function (DOMNode $n): bool {
        if ($n->nodeType !== XML_ELEMENT_NODE) return false;
        $name = strtoupper($n->nodeName);
        // treat these as block-level for our purposes
        return in_array($name, ['P','H1','H2','H3','H4','H5','H6','SHORTCODE'], true);
    };

    // Helper: is ignorable whitespace text
    $isIgnorableText = function (DOMNode $n): bool {
        return $n->nodeType === XML_TEXT_NODE && trim($n->nodeValue) === '';
    };

    // 3) Walk the root children and wrap inline/text runs into <p>
    // Because we will be modifying the DOM, first copy the node references
    $children = [];
    foreach (iterator_to_array($root->childNodes) as $node) {
        $children[] = $node;
    }

    $buffer = []; // collect inline/text nodes between blocks

    $flushBufferAsParagraph = function () use (&$buffer, $doc, $root) {
        if (empty($buffer)) return;

        // Build <p> and move buffered nodes into it
        $p = $doc->createElement('p');
        foreach ($buffer as $n) {
            $p->appendChild($n); // this will reparent the node
        }
        $buffer = [];

        // Insert at the end of root
        $root->appendChild($p);
    };

    // We rebuild the order by moving nodes; to preserve original order,
    // we’ll remove everything then append back in sequence.
    foreach ($children as $n) {
        $root->removeChild($n);
    }

    foreach ($children as $n) {
        // Skip pure whitespace text nodes between blocks
        if ($isIgnorableText($n)) {
            continue;
        }

        if ($isBlock($n)) {
            // Flush buffered inline/text as a paragraph before a block
            $flushBufferAsParagraph();

            // Append the block as-is
            $root->appendChild($n);
        } else {
            // Inline/text (STRONG, EM, A, SPAN, #text, IMG, etc.) → buffer
            $buffer[] = $n;
        }
    }

    // Flush any trailing inline/text run
    $flushBufferAsParagraph();

    // 4) Remove any now-empty <p> created incidentally
    foreach ($xpath->query('//*[@id="__root__"]//p') as $p) {
        $text = trim($p->textContent ?? '');
        if ($text === '' && $p->childNodes->length === 0) {
            $p->parentNode->removeChild($p);
        }
    }

    // 5) Return innerHTML of root
    $out = '';
    foreach ($root->childNodes as $n) {
        $out .= $doc->saveHTML($n);
    }

    return $out;
}
public static function splitLongParagraphs(string $html, int $rows = 5, int $rowWidth = 95): string
{
    // Ensure we actually have <p>…</p> to work with
    $html = self::wrapInlineIntoParagraphs($html);

    $threshold = $rows * $rowWidth; // rough chars before first split

    return preg_replace_callback('/<p[^>]*>(.*?)<\/p>/is', function ($m) use ($threshold) {
        $inner = $m[1];
        $plain = trim(strip_tags($inner));
        if (mb_strlen($plain) <= $threshold) {
            return '<p>' . $inner . '</p>';
        }

        // sentence-aware (handles inline tags around the dot)
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
        $name = strtoupper($n->nodeName);
        // we only keep these as standalone blocks; they don't count toward the row calculation
        return in_array($name, ['H1','H2','H3','H4','H5','H6','SHORTCODE'], true);
    };

    $threshold = $rows * $rowWidth;

    $flushInlineToParagraphs = static function (string $inlineHtml, int $threshold) : string {
        $inlineHtml = trim($inlineHtml);
        if ($inlineHtml === '' || mb_strlen(trim(strip_tags($inlineHtml))) === 0) return '';

        // Split on sentence ends while tolerating inline tags around punctuation
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
        // skip pure whitespace nodes
        if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) === '') continue;

        if ($isBlock($node)) {
            // flush what we collected so far into paragraphs, then output the heading untouched
            $out .= $flushInlineToParagraphs($inline, $threshold);
            $inline = '';
            $out .= $doc->saveHTML($node);
        } else {
            // collect inline/text nodes to be split into paragraphs
            $out .= ''; // no-op, just clarity
            $inline .= $doc->saveHTML($node);
        }
    }

    // tail
    $out .= $flushInlineToParagraphs($inline, $threshold);

    return $out;
}

    /**
     * @param $q
     * @return mixed
     */
    public function scopeInRandomOrderByWeight($q): mixed
    {
        return $q->orderByRaw('-LOG(1-RAND()) / `' . $this->getTable() . '`.`weight`');
    }


    public function adStatistics()
{
    return $this->hasMany(\App\Models\AdStatistic::class, 'ad_id');
}


public function images()
{
    return $this->hasMany(\App\Models\AdImage::class);
}

public function getDisplayImageUrl(): ?string
{
    // 1) Prefer images from the assigned group
    $group = $this->groupRef()->with('images')->first();
    dd($group); 
    if ($group && $group->images->count() > 0) {
        $count = $group->images->count();
        $idx   = ((int) ($this->display_count ?? 0)) % $count;

        $key = $group->images[$idx]->image_url ?? null;

        if (is_string($key) && $key !== '') {
            // Absolute URL? return as-is. Otherwise build Wasabi URL.
            return preg_match('~^https?://~i', $key)
                ? $key
                : \Storage::disk('wasabi')->url(ltrim($key, '/'));
        }
    }

    // 2) Legacy fallback: single image stored on ads.image
    $legacy = $this->getAttribute('image');
    if (is_string($legacy) && $legacy !== '') {
        return preg_match('~^https?://~i', $legacy)
            ? $legacy
            : \Storage::disk('wasabi')->url(ltrim($legacy, '/'));
    }

    // 3) Nothing to show
    return null;
}


// Optionally, to compute effective weight per image:
public function getEffectiveWeightPerImage(): ?float
{
    if($this->images()->count() > 0) {
        return $this->weight / $this->images()->count();
    }
    return $this->weight;
}


}

