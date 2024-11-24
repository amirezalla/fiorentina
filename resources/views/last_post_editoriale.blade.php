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

    .editoriale-item .editoriale-item-content .editoriale-item-content-title{
        color: #111111;
        font-size: 16px;
        margin-top: 8px;
    }
</style>
@if($last_post)
    <div class="w-full editoriale-item">
        <div class="editoriale-item-head d-flex">
            <span class="editoriale-item-head-title">Editoriale</span>
        </div>
        <div class="editoriale-item-content p-2">
            <div class="w-full d-block" >
                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}" class="d-block w-100">
                    {{ RvMedia::image($last_post->image, $last_post->name, 'large') }}
                </a>
                <a href="{{ $last_post->url }}" title="{{ $last_post->name }}" class="editoriale-item-content-title">{{ $last_post->name }}</a>
            </div>
        </div>
    </div>
@endif
