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
        aria-hidden="true" style="margin-top: 100px;">
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

                        <!-- Name -->
                        <div class="form-group">
                            <label for="post_name">Name</label>
                            <input type="text" id="post_name" name="name" class="form-control">
                        </div>

                        <!-- Slug -->
                        <div class="form-group">
                            <label for="post_slug">Slug</label>
                            <input type="text" id="post_slug" name="slug" class="form-control">
                        </div>

                        <!-- Data (Scheduled Publishing) -->
                        <label>Data</label>
                        <div class="form-row align-items-center mb-3">
                            <!-- Day -->
                            <div class="col-auto">
                                <input type="number" name="day" id="post_day" class="form-control" min="1"
                                    max="31" placeholder="01" style="width: 60px;">
                            </div>
                            <!-- Month -->
                            <div class="col-auto">
                                <select name="month" id="post_month" class="form-control">
                                    <option value="1">Gen</option>
                                    <option value="2">Feb</option>
                                    <option value="3">Mar</option>
                                    <option value="4">Apr</option>
                                    <option value="5">Mag</option>
                                    <option value="6">Giu</option>
                                    <option value="7">Lug</option>
                                    <option value="8">Ago</option>
                                    <option value="9">Set</option>
                                    <option value="10">Ott</option>
                                    <option value="11">Nov</option>
                                    <option value="12">Dic</option>
                                </select>
                            </div>
                            <!-- Year -->
                            <div class="col-auto">
                                <input type="number" name="year" id="post_year" class="form-control"
                                    placeholder="2025" style="width: 80px;">
                            </div>
                            <div class="col-auto">
                                alle
                            </div>
                            <!-- Hour -->
                            <div class="col-auto">
                                <input type="number" name="hour" id="post_hour" class="form-control" min="0"
                                    max="23" placeholder="17" style="width: 60px;">
                            </div>
                            <div class="col-auto">
                                :
                            </div>
                            <!-- Minute -->
                            <div class="col-auto">
                                <input type="number" name="minute" id="post_minute" class="form-control"
                                    min="0" max="59" placeholder="22" style="width: 60px;">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="post_status">Status</label>
                            <select id="post_status" name="status" class="form-control">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <!-- Categories (Example Static List) -->
                        <div class="form-group">
                            <label for="categories">Categorie</label>
                            <div>
                                <!-- Replace with dynamic generation as needed -->
                                <label>
                                    <input type="checkbox" name="categories[]" value="1">
                                    Archivio Sondaggi
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="2">
                                    Featured
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="3">
                                    Fotoalbum
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="4">
                                    Fotogallery
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="5">
                                    Stagione 2015-16
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="6">
                                    Stagione 2016-17
                                </label><br>
                                <label>
                                    <input type="checkbox" name="categories[]" value="7">
                                    Stagione 2017-18
                                </label>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label for="post_tags">Tag</label>
                            <textarea id="post_tags" name="tags" class="form-control" placeholder="Separa i tag con delle virgole"></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
            $('.modal-backdrop').remove();

        });
    </script>


</footer>
