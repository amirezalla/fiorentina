@if ($video && $video_files->count())
    <div class="container">
        <div class="row mx-0">
            <div class="col-12 mx-auto">
                <div class="d-block w-full">
                    <a @if($video_file_urls->count()) href="{{ $video_file_urls[0] }}" @endif target="_blank">
                        <video width="100%" id="ads-video" autoplay muted data-video="{{ json_encode($video) }}"
                               data-video-files="{{ json_encode($video_files) }}"
                               data-video-file-urls="{{ json_encode($video_file_urls) }}">
                            <source src="{{ $video_files[0] }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </a>
                    {{--                    <span id="ads-video-timer" class="text-dark"></span>--}}
                    <span id="ads-video-timer" class="text-dark" style="display: none;"></span>

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
    const videoEl = document.getElementById('ads-video');
    const video = JSON.parse(videoEl.getAttribute('data-video'));
    const video_files = JSON.parse(videoEl.getAttribute('data-video-files'));
    const video_file_urls = JSON.parse(videoEl.getAttribute('data-video-file-urls'));
    const delay = video.delay ? Number(video.delay) : 0;
    let activeVideo = 0;
    let delayTimer = null;
    console.log($(videoEl),$(videoEl).closest('a'))
    videoEl.addEventListener('ended', function (e) {
        activeVideo = (++activeVideo) % video_files.length;
        if (delay) {
            startCountdown(delay, function () {
                if (video_file_urls[activeVideo]) {
                    $(videoEl).parent().setAttribute('href', video_file_urls[activeVideo]);
                }
                videoEl.src = video_files[activeVideo];
                videoEl.play();
            })
        } else {
            videoEl.src = video_files[activeVideo];
            videoEl.play();
        }
    });

    function startCountdown(duration, func) {
        let seconds = duration;
        const timerDisplay = document.getElementById('ads-video-timer');

        const countdown = setInterval(() => {
            timerDisplay.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdown);
                func();
            } else {
                seconds -= 1;
            }
        }, 1000);
    }
</script>
