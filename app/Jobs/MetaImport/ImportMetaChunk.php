<?php

namespace App\Jobs\MetaImport;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\ImportWpFeaturedImageJob;

class ImportMetaChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $startId;
    public int $endId;

    public $tries = 5;
    public $backoff = [30, 120];
    public $timeout = 120;

    public function __construct(int $startId, int $endId)
    {
        $this->startId = $startId;
        $this->endId   = $endId;
        $this->onQueue('imports');
    }

    public function handle()
    {
        DB::connection()->disableQueryLog();
        DB::connection('mysql2')->disableQueryLog();

        $POSTS_CHUNK = 200;

        Post::query()
            ->whereBetween('id', [$this->startId, $this->endId])
            ->orderBy('id')
            ->select('id', 'image') // we need image to skip already-done posts
            ->chunkById($POSTS_CHUNK, function ($posts) {

                foreach ($posts as $p) {
                    // load exactly the keys we need for THIS post
                    $meta = DB::connection('mysql2')
                        ->table('frntn_postmeta')
                        ->where('post_id', $p->id)
                        ->whereIn('meta_key', ['_thumbnail_id', '_yoast_wpseo_primary_category'])
                        ->pluck('meta_value', 'meta_key');

                    // category (NOTE: only correct if your Botble category IDs == WP term IDs)
                    $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                        ? (int) $meta['_yoast_wpseo_primary_category'] : null;

                    Post::where('id', $p->id)->update([
                        'format_type' => 'post',
                        'category_id' => $primaryCategoryId ?: null,
                        'updated_at'  => now(),
                    ]);

                    // If there's a thumbnail and the post has no image yet, dispatch the image job
                    $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                    if ($thumbId && empty($p->image)) {
                        ImportWpFeaturedImageJob::dispatch($p->id, $thumbId)->onQueue('images');
                    }

                    unset($meta);
                }

                gc_collect_cycles();
            });
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ImportMetaChunk failed', [
            'start' => $this->startId,
            'end'   => $this->endId,
            'msg'   => $e->getMessage(),
            'trace' => substr($e->getTraceAsString(), 0, 2000),
        ]);

        // auto-split big ranges on failure
        if ($this->endId - $this->startId > 200) {
            $mid = intdiv($this->startId + $this->endId, 2);
            ImportMetaChunk::dispatch($this->startId, $mid)->onQueue('imports');
            ImportMetaChunk::dispatch($mid + 1, $this->endId)->onQueue('imports');
        }
    }
}
