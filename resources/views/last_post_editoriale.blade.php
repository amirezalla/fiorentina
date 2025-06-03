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
        <div class="editoriale-item-head d-flex">
            <span class="editoriale-item-head-title px-1 text-dark"
                style="border-bottom: 6px solid #441274;font-weight: 700;">
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
                <span style="color: gray;display:block;margin-top:10px;font-weight: 700;font-family: 'Titillium Web';">

                    Editoriale / {{ $timeText }}
                </span>

                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}"
                    class="editoriale-item-content-title py-2"
                    style="font-size: x-large; font-family: 'Roboto Condensed'; color: black; font-weight: 700;line-height:1.1;letter-spacing: -0.02em;">
                    {{-- The title of the post --}}
                    {{ $last_post->name }}
                </a>
            </div>
        </div>
    </div>
@endif
