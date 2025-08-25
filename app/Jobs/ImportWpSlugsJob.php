<?php
// app/Jobs/ImportWpSlugsJob.php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Botble\Base\Models\Slug;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportWpSlugsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle()
    {
        DB::connection()->disableQueryLog();

        Post::query()
            ->orderBy('id')
            ->chunkById(2000, function ($posts) {
                $rows = [];
                foreach ($posts as $p) {
                    $rows[] = [
                        'key'            => $p->plain_slug ?: (string)$p->id,
                        'reference_id'   => $p->id,
                        'reference_type' => Post::class,
                        'created_at'     => $p->created_at ?? now(),
                        'updated_at'     => $p->updated_at ?? now(),
                    ];
                }
                if ($rows) {
                    Slug::query()->upsert(
                        $rows,
                        ['reference_id','reference_type'], // or ['key'] if key is unique in your schema
                        ['key','updated_at']
                    );
                }
            });
    }
}
