<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class OptimizeGifs extends Command
{
    protected $signature = 'images:optimize-gifs';
    protected $description = 'Optimize all GIF images in the storage directory';

    public function handle()
    {
        $optimizerChain = OptimizerChainFactory::create();
        $files = Storage::files('/'); // Adjust this path as needed

        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'gif') {
                $fullPath = Storage::path($file);
                $this->info("Optimizing: {$fullPath}");
                $optimizerChain->optimize($fullPath);
            }
        }

        $this->info('All GIF images have been optimized!');
    }
}