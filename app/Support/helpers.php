<?php
// app/Support/helpers.php

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

}