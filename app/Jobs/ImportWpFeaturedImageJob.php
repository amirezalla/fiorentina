<?php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportWpFeaturedImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $postId;
    public int $attachmentId;

    public $tries = 3;
    public $backoff = [30, 120];
    public $timeout = 120;

    public function __construct(int $postId, int $attachmentId)
    {
        $this->postId = $postId;
        $this->attachmentId = $attachmentId;
        $this->onQueue('images');
    }

    public function handle()
    {
        // skip if image already set (idempotent)
        $existing = Post::query()->where('id', $this->postId)->value('image');
        if (!empty($existing)) return;

        $att = DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('ID', $this->attachmentId)
            ->first(['ID','post_title','post_excerpt','guid']);

        if (!$att || empty($att->guid)) return;

        // meta for alt/caption
        $meta = DB::connection('mysql2')
            ->table('frntn_postmeta')
            ->where('post_id', $this->attachmentId)
            ->pluck('meta_value', 'meta_key');

        $wpAlt = $meta['_wp_attachment_image_alt'] ?? '';
        $wpMetaSer = $meta['_wp_attachment_metadata'] ?? null;
        $imgMetaCaption = '';

        if ($wpMetaSer) {
            try {
                $arr = @unserialize($wpMetaSer);
                if (is_array($arr) && isset($arr['image_meta']['caption'])) {
                    $imgMetaCaption = (string) $arr['image_meta']['caption'];
                }
            } catch (\Throwable $e) {}
        }

        // priority: caption (post_excerpt) > alt > image_meta.caption
        $alt = trim($att->post_excerpt) ?: trim($wpAlt) ?: trim($imgMetaCaption) ?: '';

        $origUrl = $att->guid;

        // preserve original filename (sanitized)
        $basename = basename(parse_url($origUrl, PHP_URL_PATH) ?? '');
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename) ?: ('image_' . $this->attachmentId . '.jpg');

        $tmpDir = storage_path('app/temp_images');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);
        $local = $tmpDir . DIRECTORY_SEPARATOR . $safeName;

        try {
            $bin = @file_get_contents($origUrl);
            if ($bin === false) return;
            file_put_contents($local, $bin);
        } catch (\Throwable $e) {
            return;
        }

        // Upload via RV Media, keeping file name and alt (if your Botble supports it)
        $upload = app('rv_media')->uploadFromPath($local, 0, 'posts', [
            'file_name' => $safeName,
            'alt'       => $alt,
        ]);

        @unlink($local);

        if (!empty($upload['error'])) return;

        $url = $upload['data']->url ?? null;
        if (!$url) return;

        Post::where('id', $this->postId)->update([
            'image'      => $url,
            'updated_at' => now(),
        ]);
    }
}
