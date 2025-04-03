@php
    use Botble\Blog\Models\Post;
@endphp
<footer class="footer position-sticky footer-transparent d-print-none">
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <div class="text-start">
            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-between">
                <div class="order-2 order-lg-1">
                    @include('core/base::partials.copyright')
                </div>
                <div class="order-1 order-lg-2">
                    @if (defined('LARAVEL_START'))
                        {{ trans('core/base::layouts.page_loaded_in_time', ['time' => round(microtime(true) - LARAVEL_START, 2)]) }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="post_id" name="post_id" value="">

    @include('core/base::partials.quick_edit', [
        'action' => route('posts.quick-edit', $post->id ?? 0),
        'postId' => $post->id ?? '',
    ])



    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).on('click', '.quick-edit-btn', function() {
            var $btn = $(this);
            var postId = $btn.data('id');

            // Open the modal
            $('#quickEditModal').modal('show');
            $('.modal-backdrop').remove();

        });
    </script>


</footer>
