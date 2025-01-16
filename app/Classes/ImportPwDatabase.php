<?php

namespace App\Classes;

use App\Jobs\ImportUserFromWpUsersDatabase;
use Botble\ACL\Models\User;
use Botble\Blog\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportPwDatabase
{
    /**
     * @return void
     */
    public function importUsers()
    {
        $usersCount = DB::connection('mysql2')->table("frntn_users")->count();
        $max_page = ceil($usersCount / 100);
        foreach (range(0, $max_page - 1) as $i) {
            $o = $i * 100;
            ImportUserFromWpUsersDatabase::dispatch($o);
        }
    }

    public function importPosts()
    {
        $postsCount = DB::connection('mysql2')->table("frntn_posts")->count();
        $max_page = ceil($postsCount / 100);
        $posts = DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('post_parent', 0)
            ->limit(10)
            ->orderByDesc('id')
            ->get()
            ->map(fn($i) => json_decode(json_encode($i), true))
            ->toArray();
        $response = Http::get('https://www.laviola.it/?p=554494');
        dd($response);
        $res = [];
        foreach ($posts as $key => $post) {
            $res[$key]['post'] = $post;
            $res[$key]['children'] = DB::connection('mysql2')
                ->table('frntn_posts')
                ->where('post_parent', $post['ID'])
                ->get()
                ->map(fn($i) => json_decode(json_encode($i), true))
                ->toArray();
            /*Post::unguard();
            Post::query()->create([
                'id' => $post['ID'],
                'author_id' => $post['post_author'],
                'author_type' => basename(User::class),
                'status' => $post['post_status'] == "publish" ? "published" : "draft",
                'created_at' => $post['post_date'],
            ]);
            Post::reguard();*/
        }
        dd($res);
    }
}
