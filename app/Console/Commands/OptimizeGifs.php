<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class OptimizeGifs extends Command
{
    protected $signature = 'images:optimize-gifs';
    protected $description = 'Optimize all GIF images in the storage directory';

    public function handle()
    {
        $files = Storage::files('/'); // Adjust this path as needed

        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'gif') {
                $fullPath = Storage::path($file);
                // $this->info("Optimizing: {$fullPath}");
                if ($this->optimizeGifWithEzgif($fullPath)) {
                    dd("Successfully optimized: {$fullPath}");
                } else {
                    dd("Failed to optimize: {$fullPath}");
                }
            }
        }


    }

    protected function optimizeGifWithEzgif($filePath)
    {
        $url = 'https://ezgif.com/optimize';

        // Prepare the file upload using CURLFile.
        $cfile = new \CURLFile($filePath, 'image/gif', basename($filePath));
        $postData = [
            'new-image' => $cfile,
            'optimize'  => 'Optimize GIF!', // This triggers the optimization
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->error('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);

        // Parse the returned HTML to find the optimized GIF URL.
        if (preg_match('/<a href="(\/optimize\/[^"]+\.gif)"/i', $response, $matches)) {
            $optimizedUrl = 'https://ezgif.com' . $matches[1];

            // Download the optimized GIF.
            $optimizedImage = file_get_contents($optimizedUrl);
            if ($optimizedImage !== false) {
                $newPath = $filePath . '.optimized.gif';
                file_put_contents($newPath, $optimizedImage);
                $this->info("Optimized GIF saved as: {$newPath}");
                return true;
            } else {
                $this->error("Failed to download the optimized GIF.");
            }
        } else {
            $this->error("Could not extract the optimized image URL from response.");
        }

        return false;
    }

}