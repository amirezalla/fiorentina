<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
    const MOBILE_HOME_HERO_25 = 40;
    const MOBILE_DOPO_FOTO_26 = 41;
    const SKIN_MOBILE = 43;


    const GROUPS = [
        self::GROUP_POPUP_DESKTOP => "DESKTOP popup desktop",

        self::GROUP_MAIN_PAGE => "DESKTOP main page",
        self::GROUP_BLOG_PAGE => "DESKTOP blog page",
        self::GROUP_BACKGROUND_PAGE => "DESKTOP background page",
        self::GROUP_DBLOG_TITLE => "DESKTOP Dblog_title",
        self::GROUP_DBLOG_AUTHOR => "DESKTOP Dblog_author",
        self::GROUP_DBLOG_P1 => "DESKTOP Dblog_P1",
        self::GROUP_DBLOG_P2 => "DESKTOP Dblog_P2",
        self::GROUP_DBLOG_P3 => "DESKTOP Dblog_P3",
        self::GROUP_DBLOG_P4 => "DESKTOP  Dblog_P4",
        self::GROUP_DBLOG_P5 => "DESKTOP Dblog_P5",
        self::GROUP_diretta_1 => "DESKTOP Diretta_1",
        self::GROUP_recentp1 => "DESKTOP recentp1",
        self::GROUP_recentp2 => "DESKTOP recentp2",
        self::GROUP_recentp3 => "DESKTOP recentp3",
        self::GROUP_recentp4 => "DESKTOP recentp4",
        self::Google_adsense => "Google n1",
        self::SIZE_230X90_DX => "SIZE_230X90_DX",
        self::SIZE_230X90_SX => "SIZE_230X90_SX",
        self::SIZE_300X250_B1 => "SIZE_300X250_B1",
        self::SIZE_300X250_C1 => "SIZE_300X250_C1",
        self::SIZE_300X250_TOP => "SIZE_300X250_TOP",
        self::SIZE_468X60_TOP_DX => "468X60 DESTRA",
        self::SIZE_468X60_TOP_SX => "468X60 SINISTRA",
        self::GRUPPO_POPUP_MOBILE => "GRUPPO_POPUP_MOBILE",
        self::MOBILE_HOME_TOP_24 => "MOBILE_HOME_TOP_24",
        self::MOBILE_POSIZIONE_1 => "MOBILE_POSIZIONE_1",
        self::MOBILE_POSIZIONE_2 => "MOBILE_POSIZIONE_2",
        self::MOBILE_POSIZIONE_3 => "MOBILE_POSIZIONE_3",
        self::MOBILE_POSIZIONE_4 => "MOBILE_POSIZIONE_4",
        self::MOBILE_POSIZIONE_5 => "MOBILE_POSIZIONE_5",
        self::SKIN_MOBILE => "SKIN_MOBILE",
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
        'status'
    ];

    /**
     * @return int
     */
    public function getWeightPercentage(): int
    {
        $sumWeight = self::query()->where('group',$this->group)->sum('weight');
        return intval((100 / $sumWeight) * $this->weight);
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

            return Storage::temporaryUrl($this->image, now()->addMinutes(15));
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
    
        return Storage::temporaryUrl($op, now()->addMinutes(15));
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


    public static function addAdsToContent($content)
    {
        // Retrieve ads from the four dblog groups plus background
        $ads = self::query()
            ->typeAnnuncioImmagine()
            ->whereIn('group', [
                self::GROUP_DBLOG_P1,
                self::GROUP_DBLOG_P2,
                self::GROUP_DBLOG_P3,
                self::GROUP_DBLOG_P4,
                self::GROUP_BACKGROUND_PAGE,
            ])
            ->get()
            ->unique('group')
            ->mapWithKeys(fn($item) => [$item->group => $item]);

        $shortCodePattern = '/<shortcode>(.*?)<\/shortcode>/';
        $adsBackgroundShortCodeRegex = '/<shortcode>\[ads-background.*?\](.*?)\[\/ads-background.*?\]<\/shortcode>/';

        // 1. Inject background-page ad before the ck-content container
        if ($ads->has(self::GROUP_BACKGROUND_PAGE)) {
            $backgroundAdHtml = view('ads.includes.background-page', ['ad' => $ads->get(self::GROUP_BACKGROUND_PAGE)])->render();
            $content = preg_replace(
                '/(<section[^>]*class="section".*?>)/i',
                $backgroundAdHtml . "$1",
                $content
            );
        }

        // 2. Break content into paragraphs and shortcodes
        preg_match_all(
            '/<shortcode>(.*?)<\/shortcode>|<p[^>]*?>([\s\S]*?)<\/p>/',
            $content,
            $matches
        );

        if (empty($matches[0])) {
            return $content;
        }

        $elements = collect($matches[0]);

        // Extract and remove shortcodes
        $shortCodes = $elements->filter(fn($item) => preg_match($shortCodePattern, $item));
        $paragraphs = $elements->diff($shortCodes)->values();

        // Chunk into 4 parts
        $chunks = $paragraphs->chunk(ceil($paragraphs->count() / 4));

        // 3. Map each chunk, append dblog-P and mobile position ads
        $assembled = $chunks->flatMap(function ($items, $idx) use ($ads) {
            $items = $items->toArray();
            switch ($idx) {
                case 0:
                    if ($ads->has(self::GROUP_DBLOG_P1)) {
                        $items[] = view('ads.includes.dblog-p1', ['ad' => $ads->get(self::GROUP_DBLOG_P1)])->render();
                        $items[] = view('ads.includes.MOBILE_POSIZIONE_1', ['ad' => $ads->get(self::MOBILE_POSIZIONE_1)])->render();
                    }
                    break;
                case 1:
                    if ($ads->has(self::GROUP_DBLOG_P2)) {
                        $items[] = view('ads.includes.dblog-p2', ['ad' => $ads->get(self::GROUP_DBLOG_P2)])->render();
                        $items[] = view('ads.includes.MOBILE_POSIZIONE_2', ['ad' => $ads->get(self::MOBILE_POSIZIONE_2)])->render();
                    }
                    break;
                case 2:
                    if ($ads->has(self::GROUP_DBLOG_P3)) {
                        $items[] = view('ads.includes.dblog-p3', ['ad' => $ads->get(self::GROUP_DBLOG_P3)])->render();
                        $items[] = view('ads.includes.MOBILE_POSIZIONE_5', ['ad' => $ads->get(self::MOBILE_POSIZIONE_5)])->render();
                    }
                    break;
                case 3:
                    if ($ads->has(self::GROUP_DBLOG_P4)) {
                        $items[] = view('ads.includes.dblog-p4', ['ad' => $ads->get(self::GROUP_DBLOG_P4)])->render();
                        $items[] = view('ads.includes.MOBILE_POSIZIONE_4', ['ad' => $ads->get(self::MOBILE_POSIZIONE_4)])->render();
                    }
                    break;
                case 4:
                    if ($ads->has(self::GROUP_DBLOG_P5)) {
                        $items[] = view('ads.includes.dblog-p5', ['ad' => $ads->get(self::GROUP_DBLOG_P5)])->render();
                    }
                    break;
            }
            return $items;
        });

        // 4. Place background shortcodes if any
        if ($shortCodes->count()) {
            $bg = $shortCodes->first(fn($item) => preg_match($adsBackgroundShortCodeRegex, $item));
            if ($bg) {
                Theme::set('has-ads-background', $bg);
                $shortCodes = $shortCodes->filter(fn($item) => !preg_match($adsBackgroundShortCodeRegex, $item));
            }
        }

        // 5. Reassemble: shortcodes + content + appended ads
        $content = $shortCodes->merge($assembled)->implode('');

        return $content;
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
        return Storage::temporaryUrl($images[$index]->image_url, now()->addMinutes(15));
    }

    // Fallback: return the single stored image (if present).
    return Storage::temporaryUrl($this->image, now()->addMinutes(15));
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

