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
    public function getImageUrl()
    {
        if ($this->type == 1) {

            return Storage::disk('laviolas3')->url($this->image);
        }
        return $this->image;

    }

    public function getOptimizedImageUrlAttribute()
{
    $url = $this->getImageUrl();

    $path = parse_url($url, PHP_URL_PATH); // "/ads-images/TdxJ32Y4H4rSpfRdthpL53GVvN7EqhW11732631979.gif"
    if (stripos($path, '.gif') !== false) {
        $fileKey = ltrim($path, '/'); // "ads-images/TdxJ32Y4H4rSpfRdthpL53GVvN7EqhW11732631979.gif"
    
        // To remove the ".gif" extension while keeping the folder, use:
        $dir = pathinfo($fileKey, PATHINFO_DIRNAME);      // "ads-images"
        $filenameWithoutExt = pathinfo($fileKey, PATHINFO_FILENAME); // "TdxJ32Y4H4rSpfRdthpL53GVvN7EqhW11732631979"
        $fileKeyWithoutExtension = $dir . '/' . $filenameWithoutExt;
        $op= $fileKeyWithoutExtension . '-optimized.gif';
    
        return Storage::disk('laviolas3')->url($op);
    }else{
        return $url;
    }        // Remove the leading slash to get the storage key


}




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
    // rows × approx chars/row → rough plain-text length before we allow a break
    $threshold = $rows * $rowWidth;

    return preg_replace_callback('/<p>(.*?)<\/p>/is', function ($m) use ($threshold) {
        $inner = $m[1];

        // Split on sentence boundaries while being tolerant to inline tags around the dot
        // (e.g., "... La Nazione.</em> ")
        $parts = preg_split(
            '/((?<=[.!?])(?:\s|<\/(?:strong|em|b|i|span|a|u|small|sup|sub|mark|code)[^>]*>)+)/iu',
            $inner,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        // Re-attach the captured boundary to the sentence it belongs to
        $sentences = [];
        $buf = '';
        foreach ($parts as $piece) {
            $buf .= $piece;
            // boundary chunks are only spaces/closing-inline-tags
            if (preg_match('/^(?:\s|<\/(?:strong|em|b|i|span|a|u|small|sup|sub|mark|code)[^>]*>)+$/iu', $piece)) {
                $sentences[] = $buf;
                $buf = '';
            }
        }
        if (trim($buf) !== '') {
            $sentences[] = $buf;
        }

        // Build new <p> chunks: once we pass the ~5-row threshold,
        // stop at the FIRST full stop (i.e., at the end of that sentence).
        $chunks = [];
        $acc = '';
        foreach ($sentences as $s) {
            $acc .= $s;

            $plainLen = mb_strlen(trim(strip_tags($acc)));
            if ($plainLen >= $threshold) {
                // cut here (right after the sentence we just appended)
                $chunks[] = trim($acc);
                $acc = '';
            }
        }
        if (trim($acc) !== '') {
            $chunks[] = trim($acc);
        }

        // Re-wrap chunks back into <p>…</p>
        $out = '';
        foreach ($chunks as $c) {
            $out .= '<p>' . $c . '</p>';
        }
        return $out;
    }, $html);
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
    $images = $this->images;
    $count = $images->count();
    
    if ($count > 0) {
        // Calculate index using the display_count field.
        // Ensure display_count is incremented externally (see next section).
        $index = $this->display_count % $count;
        return Storage::disk('laviolas3')->url($images[$index]->image_url);
    }

    // Fallback: return the single stored image (if present).
    return Storage::disk("laviolas3")->url($this->image);
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

