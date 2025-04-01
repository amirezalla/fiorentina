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


    <script>
        $(document).on('click', '.quick-edit-btn', function() {
            var postId = $(this).data('id');
            var postName = $(this).data('name');
            var $currentRow = $(this).closest('tr');

            // Check if the next row is already our quick edit row for this post.
            if ($currentRow.next().hasClass('quick-edit-row') && $currentRow.next().data('id') == postId) {
                $currentRow.next().toggle();
            } else {
                // Remove any other quick edit rows, if needed.
                $('.quick-edit-row').remove();

                // Determine the number of columns in the current row.
                var colCount = $currentRow.find('td').length;
                // Build the quick edit form row.
                var formRow = '<tr class="quick-edit-row" data-id="' + postId + '">' +
                    '<td colspan="' + colCount + '">' +
                    '<form action="' + '{{ url('posts') }}' + '/' + postId +
                    '/quick-edit" method="POST" class="quick-edit-form">' +
                    '{{ csrf_field() }}' +
                    '<div class="form-group">' +
                    '<label for="name-' + postId + '">Post Name</label>' +
                    '<input type="text" name="name" id="name-' + postId + '" value="' + postName +
                    '" class="form-control">' +
                    '</div>' +
                    '<button type="submit" class="btn btn-primary">Save</button> ' +
                    '<button type="button" class="btn btn-secondary cancel-quick-edit" data-id="' + postId +
                    '">Cancel</button>' +
                    '</form>' +
                    '</td>' +
                    '</tr>';

                // Insert the quick edit form row immediately after the current row.
                $currentRow.after(formRow);
            }
        });

        $(document).on('click', '.cancel-quick-edit', function() {
            $(this).closest('tr').remove();
        });
    </script>


</footer>
