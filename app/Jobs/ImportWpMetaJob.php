<?php
// app/Jobs/ImportWpMetaJob.php
namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportWpMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle()
    {
        // turn off logs on BOTH connections to save memory
        DB::connection()->disableQueryLog();
        DB::connection('mysql2')->disableQueryLog();

        // knobs – tune down if needed
        $POSTS_CHUNK   = 300;  // posts per iteration
        $UPSERT_BATCH  = 120;  // posts per upsert slice

        $NEEDED_KEYS = ['_thumbnail_id', '_yoast_wpseo_primary_category'];

        Post::query()
            ->orderBy('id')
            ->select('id') // tiny select
            ->chunkById($POSTS_CHUNK, function ($posts) use ($NEEDED_KEYS, $UPSERT_BATCH) {

                $ids = $posts->pluck('id')->values()->all();
                if (!$ids) return;

                // Pull ONLY the 2 keys we need, for these post IDs
                $rawMeta = DB::connection('mysql2')
                    ->table('frntn_postmeta')
                    ->select('post_id', 'meta_key', 'meta_value')
                    ->whereIn('post_id', $ids)
                    ->whereIn('meta_key', $NEEDED_KEYS)
                    ->orderBy('post_id')
                    ->get();

                // Group minimal meta
                $byPost = [];
                foreach ($rawMeta as $m) {
                    $byPost[$m->post_id][$m->meta_key] = $m->meta_value;
                }
                unset($rawMeta); // free ASAP

                $updates = [];
                foreach ($ids as $postId) {
                    $meta = $byPost[$postId] ?? [];

                    $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                        ? (int) $meta['_yoast_wpseo_primary_category']
                        : null;

                    // enqueue image job only if we actually have a thumbnail
                    $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                    if ($thumbId) {
                        ImportWpFeaturedImageJob::dispatch($postId, $thumbId)->onQueue('images');
                    }

                    $updates[] = [
                        'id'          => $postId,
                        'format_type' => 'post',
                        // NOTE: ensure your Botble category IDs match WP term IDs or map them separately
                        'category_id' => $primaryCategoryId ?: null,
                        'updated_at'  => now(),
                    ];
                }

                // upsert in small slices
                foreach (array_chunk($updates, $UPSERT_BATCH) as $slice) {
                    Post::upsert($slice, ['id'], ['format_type','category_id','updated_at']);
                }

                unset($byPost, $updates);
                gc_collect_cycles();
            });
    }
}
