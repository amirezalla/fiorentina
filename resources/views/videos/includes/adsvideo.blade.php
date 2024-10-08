@if (isset($video_urls) && $video_urls->count())

    <div class="container">
        <div class="row mx-0">
            <div class="col-12 mx-auto">
                <div class="d-block w-full">
                    <video width="100%" id="ads-video" autoplay muted data-url="{{ json_encode($video_urls) }}">
                        <source src="{{ $video_urls[0] }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-12 col-md-12 col-lg-12 mx-auto">
        <div class="alert alert-warning text-center">
            No videos available at the moment.
        </div>
    </div>
@endif
<script>
    const video = document.getElementById('ads-video');
    const urls = JSON.parse(video.getAttribute('data-url'));
    let activeVideo = 0;
    video.addEventListener('ended', function(e) {
        activeVideo = (++activeVideo) % urls.length;
        video.src = urls[activeVideo];
        video.play();
    });
</script>
