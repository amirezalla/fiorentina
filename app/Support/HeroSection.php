<?php
// app/Support/HeroSection.php
namespace App\Support;

use Botble\Blog\Models\Post;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HeroSection
{
    public static function heroPosts()
    {
        return Cache::remember('heroPosts', 18_000, function () {
            $orders = [1, 2, 3];

            $posts = Post::when(
                Post::whereIn('hero_order', $orders)->exists(),
                function ($q) use ($orders) {
                    $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                        ->whereIn('hero_order', $orders)
                        ->groupBy('hero_order');

                    return $q
                        ->with(['categories:id,name','author:id,username,first_name,last_name'])
                        ->joinSub($sub, 'latest', fn($j) =>
                            $j->on('posts.hero_order','=','latest.hero_order')
                               ->on('posts.updated_at','=','latest.max_updated'))
                        ->orderBy('posts.hero_order');
                },
                fn($q) => $q->with(['categories:id,name','author:id,username,first_name,last_name'])
                            ->latest('created_at')->take(3),
            )->get();

            foreach ($posts as $p) {
                $p->comments_count = Comment::where('reference_id',$p->id)->count();
                $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
            }

            return $posts;
        });
    }

    public static function lastRecentPosts()
    {
        return Cache::remember('lastRecentPosts', 18_000, function () {
            $orders = [4, 5, 6, 7];

            $posts = Post::when(
                Post::whereIn('hero_order', $orders)->exists(),
                function ($q) use ($orders) {
                    $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                        ->whereIn('hero_order', $orders)
                        ->groupBy('hero_order');

                    return $q
                        ->with(['categories:id,name','author:id,username,first_name,last_name'])
                        ->joinSub($sub, 'latest', fn($j) =>
                            $j->on('posts.hero_order','=','latest.hero_order')
                               ->on('posts.updated_at','=','latest.max_updated'))
                        ->orderBy('posts.hero_order');
                },
                fn($q) => $q->with(['categories:id,name','author:id,username,first_name,last_name'])
                            ->latest('created_at')->skip(3)->take(4),
            )->get();

            foreach ($posts as $p) {
                $p->comments_count = Comment::where('reference_id',$p->id)->count();
                $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
            }

            return $posts;
        });
    }
}
