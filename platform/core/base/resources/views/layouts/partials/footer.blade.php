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
                    <!-- The quick edit form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).on('click', '.quick-edit-btn', function() {
            var postId = $(this).data('id');

            $.ajax({
                url: '/posts/' + postId + '/quick-edit',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Insert the form HTML into the modal's body
                    $('#quickEditModal .modal-body').html(response.html);
                    // Show the modal
                    $('#quickEditModal').modal('show');
                },
                error: function() {
                    alert('Failed to load the quick edit form.');
                }
            });
        });
    </script>


</footer>
