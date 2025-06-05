<?php

namespace App\Jobs;

use Botble\Blog\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostPublishingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $post_id;
    private $published_at;

    public function __construct($post_id, $published_at)
    {
        $this->post_id = $post_id;
        $this->published_at = $published_at;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = Post::query()->where('status','!=',"published")->where('id',$post_id)->first();
        if ($post ) {
            $post->update([
                'status' => "published",
            ]);
        }
    }
}
