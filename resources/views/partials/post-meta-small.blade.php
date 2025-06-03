@php
    use Carbon\Carbon;
    use FriendsOfBotble\Comment\Models\Comment;

    $date = Carbon::parse($post->published_at);
    setlocale(LC_TIME, 'it_IT.UTF-8');
    Carbon::setLocale('it');
    $date->locale('it');
    $formattedDate = $date->translatedFormat('d M H:i');

    $post->comments_count = $post->comments_count ?? Comment::where('reference_id', $post->id)->count();
@endphp

<header class="post__header">
    {{-- badges --}}
    <div class="d-flex">
        @if ($post->categories->count())
            <span class="fz-14px post-group__left-purple-badge mb-1">
                {{ $post->categories->first()->name }}
            </span>
        @endif

        @if ($post->in_aggiornamento)
            <span class="post-group__left-red-badge mb-2 ml-2">
                <i class="fa fa-spinner text-white"></i> In Aggiornamento
            </span>
        @endif
    </div>

    {{-- title --}}
    <h3 class="post__title">
        <a href="{{ $post->url }}">{{ $post->name }}</a>
    </h3>

    {{-- meta line (smaller text) --}}
    <span class="text-dark mt-2 d-block" style="font-size:small;">
        <span class="fw-bold author-post" style="color:#ffffff">
            {{ $post->author->first_name }} {{ $post->author->last_name }}
        </span>
        /
        <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#ffffff">
            <i class="fa fa-comment" aria-hidden="true"></i>
            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
        </a>
        <span class="created_at" style="color:#ffffff"> /
            {{ $formattedDate }}
        </span>
    </span>
</header>
