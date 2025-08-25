<?php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportWpPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $sinceGmt;
    protected ?int $postNameMax = null;
    protected ?int $slugKeyMax  = null;

    public function __construct(?string $sinceGmt = null)
    {
        $this->sinceGmt = $sinceGmt;
        $this->onQueue('imports');
    }

    public function handle()
    {
        DB::connection()->disableQueryLog();

        $this->postNameMax = $this->getVarcharLimit(DB::getDatabaseName(), DB::getTablePrefix() . 'posts', 'name') ?? 191;
        $this->slugKeyMax  = $this->getVarcharLimit(DB::getDatabaseName(), DB::getTablePrefix() . 'slugs', 'key') ?? 120;

        $wp = DB::connection('mysql2')->table('frntn_posts')->where('post_type', 'post');
        if ($this->sinceGmt) $wp->where('post_date_gmt', '>', $this->sinceGmt);

        $wp->orderBy('ID')->chunkById(1000, function ($rows) {
            $now   = now()->format('Y-m-d H:i:s');
            $posts = [];
            $metas = [];

            foreach ($rows as $r) {
                // ---------- sanitize dates ----------
                $createdAt   = $this->normalizeWpDate($r->post_date_gmt) ?? $now;
                $updatedAt   = $this->normalizeWpDate($r->post_date_gmt) ?? $now;
                $publishedAt = $this->normalizeWpDate($r->post_date); // may be null

                // ---------- title + slug (length-safe) ----------
                $rawTitle = (string) $r->post_title;
                $name     = $this->mbLimit($rawTitle, $this->postNameMax);

                $baseSlug = (string) ($r->post_name ?: Str::slug($rawTitle));
                if ($baseSlug === '') $baseSlug = 'post-' . (int) $r->ID;
                $plainSlug = $this->mbLimit($baseSlug, $this->slugKeyMax);
                if ($plainSlug === '') $plainSlug = 'post-' . (int) $r->ID;
                if (mb_strlen($baseSlug) > $this->slugKeyMax && $this->slugKeyMax > (mb_strlen((string)$r->ID) + 1)) {
                    $plainSlug = $this->mbLimit($plainSlug, $this->slugKeyMax - (mb_strlen((string)$r->ID) + 1)) . '-' . (int)$r->ID;
                }

                $posts[] = [
                    'id'           => (int) $r->ID,
                    'name'         => $name,
                    'description'  => Str::limit(trim(strip_tags((string) $r->post_content)), 100, '...'),
                    'content'      => (string) $r->post_content,
                    'status'       => $r->post_status === 'publish' ? 'published' : 'draft',
                    'author_id'    => 1,
                    'author_type'  => 'Botble\ACL\Models\User',
                    'published_at' => $publishedAt, // can be null
                    'created_at'   => $createdAt,
                    'updated_at'   => $updatedAt,
                    'plain_slug'   => $plainSlug,
                ];

                $metas[] = [
                    'meta_key'       => 'allow_comments',
                    'meta_value'     => json_encode([(string) ($r->comment_status === 'open' ? '1' : '0')]),
                    'reference_id'   => (int) $r->ID,
                    'reference_type' => Post::class,
                    'created_at'     => $createdAt,
                    'updated_at'     => $updatedAt,
                ];
            }

            if ($posts) {
                Post::upsert(
                    $posts,
                    ['id'],
                    ['name','description','content','status','author_id','author_type','published_at','created_at','updated_at','plain_slug']
                );
            }

            if ($metas) {
                $idsInChunk = array_column($metas, 'reference_id');
                DB::table('meta_boxes')
                    ->where('reference_type', Post::class)
                    ->where('meta_key', 'allow_comments')
                    ->whereIn('reference_id', $idsInChunk)
                    ->delete();

                DB::table('meta_boxes')->insert($metas);
            }

            unset($posts, $metas);
        }, 'ID');
    }

    // ---------- helpers ----------

    /**
     * WP dates can be '0000-00-00 00:00:00' or empty. Return 'Y-m-d H:i:s' or null.
     */
    protected function normalizeWpDate($value): ?string
    {
        if (!$value) return null;
        $s = trim((string) $value);
        if ($s === '0000-00-00 00:00:00' || $s === '0000-00-00' || $s === '0000-00-00 00:00' || $s === '0000-00-00T00:00:00') {
            return null;
        }
        $ts = @strtotime($s);
        if ($ts === false || $ts <= 0) return null;
        // optional: treat epoch “1970-01-01” as invalid too
        if ($ts === 0) return null;
        return date('Y-m-d H:i:s', $ts);
    }

    protected function mbLimit(string $value, int $limit): string
    {
        if ($limit <= 0) return '';
        if (mb_strlen($value) <= $limit) return $value;
        return mb_substr($value, 0, $limit);
    }

    protected function getVarcharLimit(string $db, string $table, string $column): ?int
    {
        try {
            $row = DB::selectOne(
                "SELECT CHARACTER_MAXIMUM_LENGTH AS len
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                [$db, $table, $column]
            );
            if ($row && isset($row->len) && $row->len) return (int) $row->len;
        } catch (\Throwable $e) {}
        return null;
    }
}
