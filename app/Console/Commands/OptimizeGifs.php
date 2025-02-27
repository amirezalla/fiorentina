<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeGifs extends Command
{
    protected $signature = 'images:optimize-gifs';
    protected $description = 'Optimize all GIF images on Wasabi using Imagick';

    public function handle()
    {
        // Get files from the "wasabi" disk (adjust the path if needed)
        $files = Storage::disk('wasabi')->files();

        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'gif') {
                $this->info("Processing: {$file}");

                // Download the file from Wasabi
                $fileContent = Storage::disk('wasabi')->get($file);
                if (!$fileContent) {
                    $this->error("Could not read file: {$file}");
                    continue;
                }

                // Save the downloaded file to a temporary local file
                $tempOriginal = tempnam(sys_get_temp_dir(), 'orig_');
                file_put_contents($tempOriginal, $fileContent);

                // Optimize the GIF using Imagick
                $optimizedTempPath = $this->optimizeGifWithImagick($tempOriginal);
                if ($optimizedTempPath) {
                    // Read the optimized file content
                    $optimizedContent = file_get_contents($optimizedTempPath);

                    // Define a new filename (for example, appending '.optimized.gif')
                    $newFileName = $file . '.optimized.gif';

                    // Upload the optimized file back to Wasabi
                    Storage::disk('wasabi')->put($newFileName, $optimizedContent);
                    $this->info("Optimized file saved as: {$newFileName}");

                    // Clean up temporary files
                    unlink($tempOriginal);
                    unlink($optimizedTempPath);
                } else {
                    $this->error("Failed to optimize file: {$file}");
                }
            }
        }

        $this->info('All GIF images have been processed!');
    }

    protected function optimizeGifWithImagick($tempFilePath)
    {
        try {
            // Create an Imagick object and read the image from the temporary file
            $imagick = new \Imagick();
            $imagick->readImage($tempFilePath);

            // Coalesce the frames (ensuring every frame is complete)
            $imagick = $imagick->coalesceImages();

            // Process each frame: adjust compression quality and strip metadata
            foreach ($imagick as $frame) {
                $frame->setImageCompressionQuality(75);
                $frame->stripImage();
            }

            // Deconstruct images to remove redundant pixels between frames
            $optimized = $imagick->deconstructImages();

            // Save the optimized GIF to a new temporary file
            $tempOptimized = tempnam(sys_get_temp_dir(), 'opt_');
            $optimized->writeImages($tempOptimized, true);

            return $tempOptimized;
        } catch (\Exception $e) {
            $this->error("Error optimizing GIF: " . $e->getMessage());
            return false;
        }
    }
}
