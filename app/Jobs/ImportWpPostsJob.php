<?php

// app/Jobs/ImportWpPostsJob.php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Botble\Base\Facades\MetaBox;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportWpPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $sinceGmt;

    public function __construct(?string $sinceGmt = null)
    {
        $this->sinceGmt = $sinceGmt;
        $this->onQueue('imports');
    }

    public function handle()
    {
        // keep memory small
        DB::connection()->disableQueryLog();

        $wp = DB::connection('mysql2')->table('frntn_posts')
            ->where('post_type', 'post');

        if ($this->sinceGmt) {
            $wp->where('post_date_gmt', '>', $this->sinceGmt);
        }

        // iterate by ID to avoid misses and reduce locks
        $wp->orderBy('ID')
           ->chunkById(1000, function ($rows) {

               $now = now();
               $posts = [];
               $metas  = []; // allow_comments only (simple meta)

               foreach ($rows as $r) {
                   $posts[] = [
                       'id'           => (int) $r->ID,
                       'name'         => (string) $r->post_title,
                       'description'  => Str::limit(trim(strip_tags((string) $r->post_content)), 100, '...'),
                       'content'      => (string) $r->post_content,
                       'status'       => $r->post_status === 'publish' ? 'published' : 'draft',
                       'author_id'    => 1,
                       'author_type'  => 'Botble\ACL\Models\User',
                       'published_at' => $r->post_date ?: $now,
                       'created_at'   => $r->post_date_gmt ?: $now,
                       'updated_at'   => $r->post_date_gmt ?: $now,
                       'plain_slug'   => (string) ($r->post_name ?: Str::slug($r->post_title)),
                   ];

                   $metas[] = [
                       'meta_key'       => 'allow_comments',
                       'meta_value'     => json_encode([(string) ($r->comment_status === 'open' ? '1' : '0')]),
                       'reference_id'   => (int) $r->ID,
                       'reference_type' => Post::class,
                       'created_at'     => $r->post_date_gmt ?: $now,
                       'updated_at'     => $r->post_date_gmt ?: $now,
                   ];
               }

               // use UPSERTS — no pre-reads
               if ($posts) {
                   Post::upsert(
                       $posts,
                       ['id'], // unique key
                       [
                           'name','description','content','status',
                           'author_id','author_type','published_at',
                           'created_at','updated_at','plain_slug'
                       ]
                   );
               }

               if ($metas) {
                   // MetaBox table usually has a composite uniqueness; emulate with upsert on (reference_id, reference_type, meta_key)
                   MetaBox::query()->upsert(
                       $metas,
                       ['reference_id','reference_type','meta_key'],
                       ['meta_value','updated_at']
                   );
               }

               // free memory per chunk
               unset($posts, $metas);
           }, 'ID'); // use primary key
    }
}
