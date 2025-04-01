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
        $(document).ready(function() {
            'use strict';

            // Quick Edit button click handler
            $(document).off('click', '.quick-edit-btn').on('click', '.quick-edit-btn', function() {
                var $btn = $(this);
                var $currentRow = $btn.closest('tr');

                // Get data attributes from the button
                var postId = $btn.data('id');
                var postName = $btn.data('name');
                var postSlug = $btn.data('slug');
                var postDate = $btn.data('date');
                var postHour = $btn.data('hour');
                var postMinute = $btn.data('minute');
                var postCategories = $btn.data('categories'); // Expected to be an array
                var postTags = $btn.data('tags');
                var postStatus = $btn.data('status');

                // If the next row is already the quick-edit row for this post, toggle its visibility.
                if ($currentRow.next().hasClass('quick-edit-row') && $currentRow.next().data('id') ==
                    postId) {
                    $currentRow.next().toggle();
                    return;
                }

                // Remove any existing quick-edit rows
                $('.quick-edit-row').remove();

                // Determine the number of columns in the current row
                var colCount = $currentRow.find('td').length;

                // Build the CSRF field (assuming a meta tag exists: <meta name="csrf-token" content="...">)
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var csrfField = '<input type="hidden" name="_token" value="' + csrfToken + '">';

                // Build the Categories input HTML (adjust as needed for your project)
                var categoriesInput = '';
                if (postCategories && Array.isArray(postCategories)) {
                    categoriesInput += '<div><strong>Categories:</strong></div>';
                    postCategories.forEach(function(catId) {
                        categoriesInput += '<div class="form-check">' +
                            '<input class="form-check-input" type="checkbox" name="categories[]" value="' +
                            catId + '" checked> Category ID ' + catId +
                            '</div>';
                    });
                }

                // Build the Tags input HTML
                var tagsInput = '<div class="form-group">' +
                    '<label>Tags</label>' +
                    '<input type="text" class="form-control" name="tags" value="' + (postTags || '') +
                    '">' +
                    '</div>';

                // Build the quick edit form row
                var formRow = '<tr class="quick-edit-row" data-id="' + postId +
                    '" style="display: table-row;">' +
                    '<td colspan="' + colCount + '">' +
                    '<form action="/posts/' + postId +
                    '/quick-edit" method="POST" class="quick-edit-form p-3 bg-light border">' +
                    csrfField +
                    '<div class="row">' +
                    // Title field
                    '<div class="col-md-6 mb-3">' +
                    '<label>Title</label>' +
                    '<input type="text" class="form-control" name="name" value="' + (postName || '') +
                    '">' +
                    '</div>' +
                    // Slug field
                    '<div class="col-md-6 mb-3">' +
                    '<label>Slug</label>' +
                    '<input type="text" class="form-control" name="slug" value="' + (postSlug || '') +
                    '">' +
                    '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    // Date field
                    '<div class="col-md-3 mb-3">' +
                    '<label>Date</label>' +
                    '<input type="date" class="form-control" name="date" value="' + (postDate || '') +
                    '">' +
                    '</div>' +
                    // Hour field
                    '<div class="col-md-2 mb-3">' +
                    '<label>Hour</label>' +
                    '<input type="number" class="form-control" name="hour" value="' + (postHour || '') +
                    '" min="0" max="23">' +
                    '</div>' +
                    // Minute field
                    '<div class="col-md-2 mb-3">' +
                    '<label>Minute</label>' +
                    '<input type="number" class="form-control" name="minute" value="' + (postMinute || '') +
                    '" min="0" max="59">' +
                    '</div>' +
                    // Status field
                    '<div class="col-md-3 mb-3">' +
                    '<label>Status</label>' +
                    '<select class="form-control" name="status">' +
                    '<option value="published" ' + (postStatus === 'published' ? 'selected' : '') +
                    '>Published</option>' +
                    '<option value="draft" ' + (postStatus === 'draft' ? 'selected' : '') +
                    '>Draft</option>' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    // Categories
                    '<div class="mb-3">' +
                    categoriesInput +
                    '</div>' +
                    // Tags
                    tagsInput +
                    // Buttons
                    '<button type="submit" class="btn btn-primary">Aggiorna</button> ' +
                    '<button type="button" class="btn btn-secondary cancel-quick-edit" data-id="' + postId +
                    '">Annulla</button>' +
                    '</form>' +
                    '</td>' +
                    '</tr>';

                // Insert the quick edit form row after the current row
                $currentRow.after(formRow);
            });

            // Cancel button click handler to remove the quick edit row
            $(document).off('click', '.cancel-quick-edit').on('click', '.cancel-quick-edit', function() {
                $(this).closest('tr.quick-edit-row').remove();
            });
        });
    </script>


</footer>
