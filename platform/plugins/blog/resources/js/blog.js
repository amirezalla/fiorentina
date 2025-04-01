$(() => {
    'use strict'

    BDashboard.loadWidget($('#widget_posts_recent').find('.widget-content'), $('#widget_posts_recent').data('url'));

    // When a Quick Edit button is clicked, insert a new hidden row with the form.
    $(document).on('click', '.quick-edit-btn', function () {
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
            // Build the CSRF hidden field using the meta tag.
            var csrfField = '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">';
            // Build the quick edit form row.
            var formRow = '<tr class="quick-edit-row" data-id="' + postId + '">' +
                '<td colspan="' + colCount + '">' +
                '<form action="/posts/' + postId + '/quick-edit" method="POST" class="quick-edit-form">' +
                csrfField +
                '<div class="form-group">' +
                '<label for="name-' + postId + '">Post Name</label>' +
                '<input type="text" name="name" id="name-' + postId + '" value="' + postName + '" class="form-control">' +
                '</div>' +
                '<button type="submit" class="btn btn-primary">Save</button> ' +
                '<button type="button" class="btn btn-secondary cancel-quick-edit" data-id="' + postId + '">Cancel</button>' +
                '</form>' +
                '</td>' +
                '</tr>';

            // Insert the quick edit form row immediately after the current row.
            $currentRow.after(formRow);
        }
    });

    // Remove the quick edit form row when Cancel is clicked.
    $(document).on('click', '.cancel-quick-edit', function () {
        $(this).closest('tr').remove();
    });
});
