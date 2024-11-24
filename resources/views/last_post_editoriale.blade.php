<style>
    .editoriale-item{
        width: 100%;
    }

    .editoriale-item .editoriale-item-head{
        border-bottom: 1px solid #eeeeee;
    }

    .editoriale-item .editoriale-item-head .editoriale-item-head-title{
        font-size: 18px;
        font-weight: 500;
        color: #111111;
        padding: 0 8px;
        position: relative;
    }

    .editoriale-item .editoriale-item-head .editoriale-item-head-title::before{
        height: 2px;
        left: 0;
        right: 0;
        top: 100%;
        position: absolute;
        content: "";
        background-color: rgb(68, 18, 116);
    }

    .editoriale-item .editoriale-item-content-link{
        width: 100%;
    }

    .editoriale-item .editoriale-item-content-link .editoriale-item-content-link-title{
        font-size: 16px;
    }
</style>
@if($last_post)
    <div class="w-full editoriale-item">
        <div class="editoriale-item-head d-flex">
            <span class="editoriale-item-head-title">Editoriale</span>
        </div>
        <div class="editoriale-item-content p-2">
            <a href="{{ $last_post->url }}" class="w-full editoriale-item-content-link d-block" title="{{ $last_post->name }}">
                {{ RvMedia::image($last_post->image, $last_post->name, 'large') }}
                <div class="editoriale-item-content-link-title">{{ $last_post->name }}</div>
            </a>
        </div>
    </div>
@endif
