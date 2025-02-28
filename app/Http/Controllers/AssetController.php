<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function getAsset(Request $request, $path)
    {
        // Create a cache key for the asset (you might want to include a version if assets change)
        $cacheKey = 'asset:' . $path;
        
        // Attempt to retrieve the asset content from Redis cache for 12 hours
        $assetContent = Cache::store('redis')->remember($cacheKey, now()->addHours(12), function () use ($path) {
            // Generate a temporary URL valid for 15 minutes
            $temporaryUrl = Storage::disk('wasabi')->temporaryUrl($path, now()->addMinutes(15));
            
            // Fetch the asset content from the temporary URL
            return file_get_contents($temporaryUrl);
        });
        
        // Optionally, get the MIME type (or hard-code if you know the asset type)
        $mimeType = Storage::disk('wasabi')->mimeType($path);
        
        // Return the asset content with the appropriate Content-Type header
        return response($assetContent, 200)->header('Content-Type', $mimeType);
    }
}
