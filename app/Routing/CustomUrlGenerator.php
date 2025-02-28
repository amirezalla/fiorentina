<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

class CustomUrlGenerator extends BaseUrlGenerator
{
    /**
     * Generate an asset URL for the given path.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        // Check if the path starts with the Wasabi/S3 domain.
        if (strpos($path, 'https://laviola.s3.eu-south-1.wasabisys.com/') === 0) {
            // Parse the URL to extract the path portion (ignoring query parameters).
            $parsed = parse_url($path);
            if (isset($parsed['path'])) {
                $cleanPath = ltrim($parsed['path'], '/');
                // Return the locally routed asset URL.
                return url('/asset/' . $cleanPath);
            }
        }

        // Otherwise, use the default behavior.
        return parent::asset($path, $secure);
    }
}
