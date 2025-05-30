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
        'postId' => $post->id ?? '',
    ])



    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel = "stylesheet" type = "text/css" href = "https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        $(document).on('click', '.quick-edit-btn', function() {
            var postId = $(this).data('id');

            $.ajax({
                url: '/posts/quick-edit-form/' + postId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Inject the rendered HTML into a container
                    // (Assuming your modal is defined in the partial)
                    $('body').append(response.html);
                    // Show the modal
                    $('#quickEditForm').attr('action', '/posts/' + postId + '/quick-edit');

                    $('#quickEditModal').modal('show');
                    $('.modal-backdrop').remove();
                },
                error: function() {
                    alert('Error loading quick edit form.');
                }
            });
        });




        // Listen for the quick edit form submission
        $(document).on('submit', '#quickEditForm', function(e) {
            e.preventDefault(); // Prevent normal form submission

            var $form = $(this);
            var url = $form.attr('action');
            var formData = $form.serialize();

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Optionally, you might refresh your table here

                    // Close the modal
                    $('#quickEditModal').modal('hide');

                    // Show success toast
                    Toastify({
                        text: "Post updated successfully!",
                        duration: 3000,
                        gravity: "top", // top or bottom
                        position: "right", // left, center or right
                        backgroundColor: "#28a745",

                        stopOnFocus: true, // Stop if user hovers over toast
                    }).showToast();
                    setTimeout(function() {
                        $('[data-bb-target=".buttons-reload"]').trigger('click');
                    }, 500);
                },
                error: function(xhr, status, error) {
                    // Show error toast
                    Toastify({
                        text: "Error updating post: " + error,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        stopOnFocus: true,
                    }).showToast();
                }
            });
        });
        $(document).on('click', '.modal .close', function() {
            $(this).closest('.modal').modal('hide');
        });



        $(document).ready(function() {
            $(document).on('click', '[data-action="restore"]', function(e) {
                e.preventDefault();

                // Gather selected IDs from table checkboxes inside rows with class "selected"
                const selectedIds = $('tr.selected input[name="id[]"]').map(function() {
                    return $(this).val();
                }).get();
                console.log(selectedIds);

                if (!selectedIds.length) {
                    alert('No items selected.');
                    return;
                }

                $.ajax({
                    url: "{{ route('posts.bulk-restore') }}",
                    method: "POST",
                    data: {
                        ids: selectedIds,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Optionally, show a notification or refresh your table
                        // For example, reload the page:
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("Error restoring posts: " + error);
                    }
                });
            });
            $(document).on('click', '[data-action="bulk-delete"]', function(e) {
                e.preventDefault();

                // Gather selected IDs from table checkboxes inside rows with class "selected"
                const selectedIds = $('tr.selected input[name="id[]"]').map(function() {
                    return $(this).val();
                }).get();
                console.log(selectedIds);

                if (!selectedIds.length) {
                    alert('No items selected.');
                    return;
                }

                $.ajax({
                    url: "{{ route('posts.bulk-delete') }}",
                    method: "POST",
                    data: {
                        ids: selectedIds,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Optionally, show a notification or refresh your table
                        // For example, reload the page:
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("Error restoring posts: " + error);
                    }
                });
            });
        });
    </script>


</footer>
