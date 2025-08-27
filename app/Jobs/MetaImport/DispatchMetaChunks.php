<?php

namespace App\Jobs\MetaImport;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchMetaChunks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle()
    {
        DB::connection()->disableQueryLog();

        // Create small ID ranges so a failure doesn't kill the whole run
        $CHUNK_SIZE = 1500;

        $min = (int) Post::min('id');
        $max = (int) Post::max('id');
        if (!$min || !$max) return;

        for ($start = $min; $start <= $max; $start += $CHUNK_SIZE) {
            $end = min($start + $CHUNK_SIZE - 1, $max);
            ImportMetaChunk::dispatch($start, $end)->onQueue('imports');
        }
    }
}
