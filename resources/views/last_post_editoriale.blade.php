@if($last_post)
    <div class="w-full editoriale-item mt-30">
        <div class="editoriale-item-head d-flex">
            <span class="editoriale-item-head-title">Editoriale</span>
        </div>
        <div class="editoriale-item-content p-2">
            <div class="w-full d-block" >
                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}" class="d-block w-100">
                    {{ RvMedia::image($last_post->image, $last_post->name, 'large') }}
                </a>
                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}" class="editoriale-item-content-title p-3">{{ $last_post->name }}</a>
            </div>
        </div>
    </div>
@endif
