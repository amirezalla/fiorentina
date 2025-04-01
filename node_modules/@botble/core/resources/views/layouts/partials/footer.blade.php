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
    <div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" aria-labelledby="quickEditModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickEditModalLabel">Quick Edit Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quickEditForm" method="POST" action="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="post_id" name="id" value="">
                        <div class="form-group">
                            <label for="post_name">Name</label>
                            <input type="text" id="post_name" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="post_slug">Slug</label>
                            <input type="text" id="post_slug" name="slug" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="post_date">Date</label>
                            <input type="date" id="post_date" name="date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="post_hour">Hour</label>
                            <input type="number" id="post_hour" name="hour" class="form-control" min="0"
                                max="23">
                        </div>
                        <div class="form-group">
                            <label for="post_minute">Minute</label>
                            <input type="number" id="post_minute" name="minute" class="form-control" min="0"
                                max="59">
                        </div>
                        <div class="form-group">
                            <label for="post_status">Status</label>
                            <select id="post_status" name="status" class="form-control">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <!-- Optionally, you can add categories and tags fields if needed -->
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).on('click', '.quick-edit-btn', function() {
            var $btn = $(this);
            var postId = $btn.data('id');
            var postName = $btn.data('name');
            var postSlug = $btn.data('slug');
            var postDate = $btn.data('date');
            var postHour = $btn.data('hour');
            var postMinute = $btn.data('minute');
            var postStatus = $btn.data('status');

            // Set the form action URL (adjust the URL to your route)
            $('#quickEditForm').attr('action', '/posts/' + postId + '/quick-edit');

            // Populate form fields with data from the button
            $('#post_id').val(postId);
            $('#post_name').val(postName);
            $('#post_slug').val(postSlug);
            $('#post_date').val(postDate);
            $('#post_hour').val(postHour);
            $('#post_minute').val(postMinute);
            $('#post_status').val(postStatus);

            // Open the modal
            $('#quickEditModal').modal('show');
        });
    </script>


</footer>
