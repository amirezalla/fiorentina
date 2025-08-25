<?php
// app/Jobs/ImportWpMetaJob.php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        DB::connection()->disableQueryLog();

        // work over all posts (or narrow: whereNull('category_id') if you want)
        Post::query()
            ->orderBy('id')
            ->chunkById(800, function ($posts) {

                $ids = $posts->pluck('id')->all();

                // Pull all postmeta for this batch at once
                $rawMeta = DB::connection('mysql2')
                    ->table('frntn_postmeta')
                    ->whereIn('post_id', $ids)
                    ->select('post_id', 'meta_key', 'meta_value')
                    ->get();

                // Group meta by post_id into an array [post_id => [key => value]]
                $byPost = [];
                foreach ($rawMeta as $m) {
                    $byPost[$m->post_id][$m->meta_key] = $m->meta_value;
                }
                unset($rawMeta);

                $updates = [];

                foreach ($posts as $post) {
                    $meta = $byPost[$post->id] ?? [];

                    // Yoast primary category (string id)
                    $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                        ? (int) $meta['_yoast_wpseo_primary_category'] : null;

                    // queue featured image import if present
                    $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                    if ($thumbId) {
                        ImportWpFeaturedImageJob::dispatch($post->id, $thumbId)->onQueue('images');
                    }

                    $updates[] = [
                        'id'           => $post->id,
                        'format_type'  => 'post',
                        // WARNING: you may need a mapping from WP term id to your Botble categories table
                        'category_id'  => $primaryCategoryId ?: null,
                        'updated_at'   => now(),
                    ];
                }

                if ($updates) {
                    Post::upsert($updates, ['id'], ['format_type','category_id','updated_at']);
                }

                unset($byPost, $updates);
            });
    }
}
