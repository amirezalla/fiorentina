<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeGifs extends Command
{
    protected $signature = 'images:optimize-gifs';
    protected $description = 'Optimize all GIF images in the storage directory using Imagick';

    public function handle()
    {
        $files = Storage::files('/'); // Adjust this path as needed

        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'gif') {
                $fullPath = Storage::path($file);
                $this->info("Optimizing: {$fullPath}");
                if ($this->optimizeGifWithImagick($fullPath)) {
                    $this->info("Successfully optimized: {$fullPath}");
                } else {
                    $this->error("Failed to optimize: {$file}");
                }
            }
        }

        $this->info('All GIF images have been processed!');
    }

    protected function optimizeGifWithImagick($filePath)
    {
        try {
            // Read the GIF
            $imagick = new \Imagick();
            $imagick->readImage($filePath);
            
            // Coalesce frames (to work on each frame independently)
            $imagick = $imagick->coalesceImages();

            // Optionally, adjust quality or strip metadata from each frame
            foreach ($imagick as $frame) {
                // Set a lower compression quality (0-100, lower is smaller)
                $frame->setImageCompressionQuality(75);
                // Remove extraneous metadata
                $frame->stripImage();
            }

            // Deconstruct the frames to optimize (remove redundant pixels)
            $optimized = $imagick->deconstructImages();

            // Save the optimized GIF (append .optimized.gif to the filename)
            $newPath = $filePath . '.optimized.gif';
            $optimized->writeImages($newPath, true);

            return true;
        } catch (\Exception $e) {
            $this->error("Error optimizing GIF: " . $e->getMessage());
            return false;
        }
    }
}
