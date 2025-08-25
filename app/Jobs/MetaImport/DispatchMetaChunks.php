<?php
// app/Jobs/MetaImport/DispatchMetaChunks.php
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

    public $tries   = 1;      // splitter is cheap
    public $timeout = 60;     // seconds

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle()
    {
        DB::connection()->disableQueryLog();

        // Make N chunk jobs by ID ranges. Tune CHUNK_SIZE if needed.
        $CHUNK_SIZE = 2000;

        $min = (int) Post::min('id');
        $max = (int) Post::max('id');
        if (!$min || !$max) return;

        for ($start = $min; $start <= $max; $start += $CHUNK_SIZE) {
            $end = min($start + $CHUNK_SIZE - 1, $max);
            ImportMetaChunk::dispatch($start, $end)->onQueue('imports');
        }
    }
}
