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

    public $tries   = 5;          // allow a few retries
    public $backoff = [30, 120];  // seconds
    public $timeout = 120;        // seconds

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

        // process posts in tiny slices and only fetch the 2 keys we need
        $POSTS_CHUNK  = 200;  // posts read per sub-iteration
        $UPSERT_BATCH = 80;   // posts per upsert slice
        $NEEDED_KEYS  = ['_thumbnail_id', '_yoast_wpseo_primary_category'];

        Post::query()
            ->whereBetween('id', [$this->startId, $this->endId])
            ->orderBy('id')
            ->select('id')
            ->chunkById($POSTS_CHUNK, function ($posts) use ($NEEDED_KEYS, $UPSERT_BATCH) {

                $ids = $posts->pluck('id')->values()->all();
                if (!$ids) return;

                // grab only the needed meta keys for these ids
                $rawMeta = DB::connection('mysql2')
                    ->table('frntn_postmeta')
                    ->select('post_id', 'meta_key', 'meta_value')
                    ->whereIn('post_id', $ids)
                    ->whereIn('meta_key', $NEEDED_KEYS)
                    ->orderBy('post_id')
                    ->get();

                $byPost = [];
                foreach ($rawMeta as $m) {
                    $byPost[$m->post_id][$m->meta_key] = $m->meta_value;
                }
                unset($rawMeta);

                $updates = [];
                foreach ($ids as $postId) {
                    $meta = $byPost[$postId] ?? [];

                    $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                        ? (int) $meta['_yoast_wpseo_primary_category'] : null;

                    $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                    if ($thumbId) {
                        ImportWpFeaturedImageJob::dispatch($postId, $thumbId)->onQueue('images');
                    }

                    $updates[] = [
                        'id'          => $postId,
                        'format_type' => 'post',
                        // WARNING: only valid if your Botble category IDs == WP term IDs
                        'category_id' => $primaryCategoryId ?: null,
                        'updated_at'  => now(),
                    ];
                }

                foreach (array_chunk($updates, $UPSERT_BATCH) as $slice) {
                    Post::upsert($slice, ['id'], ['format_type', 'category_id', 'updated_at']);
                }

                unset($byPost, $updates);
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
        // Optional: split this range in half and re-dispatch smaller chunks:
        if ($this->endId - $this->startId > 200) {
            $mid = intdiv($this->startId + $this->endId, 2);
            ImportMetaChunk::dispatch($this->startId, $mid)->onQueue('imports');
            ImportMetaChunk::dispatch($mid + 1, $this->endId)->onQueue('imports');
        }
    }
}