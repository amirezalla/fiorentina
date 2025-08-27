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

    // Keep the worker gentle
    public $tries   = 5;
    public $backoff = [30, 120];
    public $timeout = 120;

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle()
    {
        // disable logs on BOTH DBs
        DB::connection()->disableQueryLog();
        DB::connection('mysql2')->disableQueryLog();

        // process tiny chunks of posts, but stream EACH post’s meta individually
        $POSTS_CHUNK = 200;

        Post::query()
            ->orderBy('id')
            ->select('id')
            ->chunkById($POSTS_CHUNK, function ($posts) {

                foreach ($posts as $p) {
                    // read exactly the two keys we need for this post
                    $meta = DB::connection('mysql2')
                        ->table('frntn_postmeta')
                        ->where('post_id', $p->id)
                        ->whereIn('meta_key', ['_thumbnail_id', '_yoast_wpseo_primary_category'])
                        ->pluck('meta_value', 'meta_key');

                    // category (NOTE: only correct if IDs match between WP and Botble)
                    $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                        ? (int) $meta['_yoast_wpseo_primary_category'] : null;

                    Post::where('id', $p->id)->update([
                        'format_type' => 'post',
                        'category_id' => $primaryCategoryId ?: null,
                        'updated_at'  => now(),
                    ]);

                    // store thumbnail id for later, don’t dispatch image job here
                    $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                    DB::table('wp_post_thumbs')->updateOrInsert(
                        ['post_id' => $p->id],
                        ['thumb_id' => $thumbId, 'updated_at' => now(), 'created_at' => now()]
                    );

                    // free as we go
                    unset($meta);
                }

                gc_collect_cycles();
            });
    }
}