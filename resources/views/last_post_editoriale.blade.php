@if ($last_post)
    @php
        // Make sure you have Carbon imported at the top of your file if needed:
        // use Carbon\Carbon;

        // Force Carbon to use the Italian locale for date differences:
        \Carbon\Carbon::setLocale('it');

        // Generate "15 ore fa", "3 giorni fa", "1 settimana fa", etc.
        $timeText = $last_post->created_at->diffForHumans();
    @endphp

    <div class="w-full editoriale-item mt-30">
        <div class="editoriale-item-head d-flex" style="border-bottom: 2px solid #ccc">
            <span class="editoriale-item-head-title px-1 text-dark heading-container"
                style="border-bottom: 2px solid #8424e3;margin-bottom:-2px;">
                {{-- The title of the section --}}
                EDITORIALE
            </span>
        </div>
        <div class="editoriale-item-content p-2">
            <div class="w-full d-block">
                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}" class="d-block w-100">
                    {{ RvMedia::image(
                        $last_post->image,
                        $last_post->name,
                        'medium',
                        attributes: [
                            'loading' => 'lazy',
                        ],
                    ) }}
                </a>

                {{-- The new line in gray: "editoriali / X ore fa" --}}
                <span
                    style="color: gray;display:block;margin-top:10px;margin-bottom:10px;font-weight: 700;font-family: 'Titillium Web';">

                    <a href="/editoriali" class="text-muted fw-bold"> Editoriale </a> / {{ $timeText }}
                </span>

                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}"
                    class="editoriale-item-content-title py-2"
                    style="font-size: x-large; font-family: 'Roboto Condensed'; color: black; font-weight: 700;line-height:1.1;letter-spacing: -0.02em;">
                    {{-- The title of the post --}}
                    {{ $last_post->name }}
                </a>
                <span class=" text-dark mt-1 d-block"
                    style="font-family: 'Titillium Web', sans-serif; font-weight: 600; font-size: 16px;color:#888">
                    @php
                        $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                            'reference_id',
                            $post->id,
                        )->count();
                    @endphp
                    Di <a style="color: #8424e3;font-weight: 700;"
                        href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                        {{ $post->author->last_name }}</a> /
                    <a class="fw-bold" href="{{ $post->url }}#comments"
                        style="color:#8424e3;font-size:0.9rem !important;">
                        <i class="fa fa-comment" aria-hidden="true"></i>
                        {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                    </a>
                </span>
            </div>
        </div>
    </div>
@endif
