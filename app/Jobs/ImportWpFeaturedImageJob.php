<?php
// app/Jobs/ImportWpFeaturedImageJob.php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Support\Str;

class ImportWpFeaturedImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $postId;
    public int $attachmentId;

    public function __construct(int $postId, int $attachmentId)
    {
        $this->postId = $postId;
        $this->attachmentId = $attachmentId;
        $this->onQueue('images');
    }

    public function handle()
    {
        // Get attachment row (guid, title/caption etc.)
        $att = DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('ID', $this->attachmentId)
            ->first(['ID','post_title','post_excerpt','guid']);

        if (!$att || empty($att->guid)) {
            return;
        }

        // Prefer: caption from post_excerpt, else meta alt, else image_meta.caption
        $meta = DB::connection('mysql2')
            ->table('frntn_postmeta')
            ->where('post_id', $this->attachmentId)
            ->pluck('meta_value', 'meta_key');

        $wpAlt     = $meta['_wp_attachment_image_alt'] ?? '';
        $wpMetaSer = $meta['_wp_attachment_metadata'] ?? null;
        $imgMetaCaption = '';

        if ($wpMetaSer) {
            // very lightweight serialized read (no unserialize if you’re cautious)
            // but we can attempt safely:
            try {
                $arr = @unserialize($wpMetaSer);
                if (is_array($arr) && isset($arr['image_meta']['caption'])) {
                    $imgMetaCaption = (string) $arr['image_meta']['caption'];
                }
            } catch (\Throwable $e) {}
        }

        // Your rule: "some have the caption saved in DB; save it to alt"
        // Priority: post_excerpt (caption) > _wp_attachment_image_alt > image_meta.caption
        $alt = trim($att->post_excerpt) ?: trim($wpAlt) ?: trim($imgMetaCaption) ?: '';

        // Download to temp
        $origUrl = $att->guid;
        $tmp = storage_path('app/temp_images');
        if (!is_dir($tmp)) {
            @mkdir($tmp, 0755, true);
        }

        $basename = basename(parse_url($origUrl, PHP_URL_PATH) ?? '');
        // Keep the original name (sanitize dangerous chars)
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename) ?: ('image_' . $this->attachmentId . '.jpg');

        $localPath = $tmp . DIRECTORY_SEPARATOR . $safeName;

        try {
            $bin = @file_get_contents($origUrl);
            if ($bin === false) {
                return;
            }
            file_put_contents($localPath, $bin);
        } catch (\Throwable $e) {
            return;
        }

        // Upload via RV Media, preserving file name
        // RvMedia::uploadFromPath(path, folder_id, folder_slug, options)
        // Many installs accept 'file_name' & 'alt' in options; adjust if your version differs.
        $upload = app('rv_media')->uploadFromPath($localPath, 0, 'posts', [
            'file_name' => $safeName,
            'alt'       => $alt,
        ]);

        @unlink($localPath);

        if (!empty($upload['error'])) {
            return;
        }

        $url = $upload['data']->url ?? null;
        if (!$url) return;

        Post::where('id', $this->postId)->update([
            'image'      => $url,
            'updated_at' => now(),
        ]);
    }
}
