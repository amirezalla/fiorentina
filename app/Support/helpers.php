<?php
// app/Support/helpers.php
use App\Support\AdRequestContext;

if (! function_exists('request_is_mobile')) {
    /**
     * Very lightweight mobile check.
     * Good enough for choosing a small/large image server-side.
     */
    function request_is_mobile(): bool
    {
        $ua = request()->header('User-Agent', '');

        return (bool) preg_match(
            '/android|iphone|ipod|ipad|blackberry|bb10|mini|windows\sce|palm/i',
            $ua
        );
    }
    function yt_id(string $url): string
{
    preg_match('%(?:youtu\\.be/|v=|embed/)([\\w-]{11})%i', $url, $m);
    return $m[1] ?? $url;     // accept plain IDs too
}

if (! function_exists('ad_ctx')) {
    function ad_ctx(): AdRequestContext
    {
        return app(AdRequestContext::class);
    }
}

if (! function_exists('ad_img')) {
    function ad_img(int $slotConst): ?string
    {
        return ad_ctx()->img($slotConst);
    }
}

if (! function_exists('ad_href')) {
    function ad_href(int $slotConst): ?string
    {
        return ad_ctx()->href($slotConst);
    }
}

}