@php
    use Carbon\Carbon;
    use FriendsOfBotble\Comment\Models\Comment;
    use Illuminate\Support\Facades\DB;
    use Botble\Blog\Models\Post;

    // --- recupero post ----------------------------------------------------
    $heroOrders = [4, 5, 6, 7];

    $posts = Post::with('categories:id,name')
        ->when(
            Post::whereIn('hero_order', $heroOrders)->exists(),
            function ($q) use ($heroOrders) {
                $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                    ->whereIn('hero_order', $heroOrders)
                    ->groupBy('hero_order');

                $q->joinSub(
                    $sub,
                    'latest',
                    fn($j) => $j
                        ->on('posts.hero_order', '=', 'latest.hero_order')
                        ->on('posts.updated_at', '=', 'latest.max_updated'),
                )->orderBy('posts.hero_order');
            },
            fn($q) => $q->orderByDesc('created_at')->skip(3)->take(4),
        )
        ->get();
@endphp

<div class="black-box px-3 py-3">
    <div class="d-flex flex-column justify-content-around h-100">
        @foreach ($posts as $post)
            @php
                $date = Carbon::parse($post->published_at)->locale('it');
                $formattedDate = $date->translatedFormat('d M H:i');

                // conta i commenti una sola volta
                $post->comments_count = $post->comments_count ?? Comment::where('reference_id', $post->id)->count();
            @endphp

            <article class="w-100 @unless ($loop->last) mb-3 @endunless">
                <header class="post__last4">
                    {{-- badge categoria --}}
                    @if ($post->categories->count())
                        <div class="d-flex mb-1">
                            <span class="post__last4-badge">
                                {{ $post->categories->first()->name }}
                            </span>
                        </div>
                    @endif

                    {{-- titolo --}}
                    <a class="post__last4-text" href="{{ $post->url }}">
                        {{ $post->name }}
                    </a>

                    {{-- meta riga: autore / commenti / data --}}
                    <span class="text-dark mt-2 d-block" style="font-size:x-small;">
                        <span class="fw-bold author-post" style="color:#ffffff">
                            {{ $post->author->first_name }} {{ $post->author->last_name }}
                        </span> /
                        <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#ffffff">
                            <i class="fa fa-comment"></i>
                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                        </a>
                        <span class="created_at" style="color:#ffffff"> /
                            {{ $formattedDate }}
                        </span>
                    </span>
                </header>
            </article>
        @endforeach
    </div>
</div>
