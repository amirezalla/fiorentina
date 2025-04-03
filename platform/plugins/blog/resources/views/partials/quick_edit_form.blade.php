<tr id="quick-edit-row-{{ $post->id }}" class="quick-edit-row" style="display: none;">
    <td colspan="7">
        <form action="{{ route('posts.quick-edit', $post->id) }}" method="POST" class="quick-edit-form">
            @csrf
            <!-- Add the fields you want to quick edit -->
            <div class="form-group">
                <label for="name-{{ $post->id }}">Post Name</label>
                <input type="text" name="name" id="name-{{ $post->id }}" value="{{ $post->name }}"
                    class="form-control">
            </div>
            <!-- You can add more fields here -->
            <!-- Name -->
            <div class="form-group">
                <label for="post_name">Name</label>
                <input type="text" id="post_name" name="name" class="form-control" value="{{ $name ?? '' }}">
            </div>

            <!-- Slug -->
            <div class="form-group">
                <label for="post_slug">Slug</label>
                <input type="text" id="post_slug" name="slug" class="form-control" value="{{ $slug ?? '' }}">
            </div>

            <!-- Data (Scheduled Publishing) -->
            <div class="form-group">
                <label>Data</label>
                <div class="row">
                    <!-- Day -->
                    <div class="col-4 col-sm-4 col-md-2 mb-2">
                        <input type="number" name="day" id="post_day" class="form-control" placeholder="01"
                            min="1" max="31" value="{{ $day ?? '' }}">
                    </div>
                    <!-- Month -->
                    <div class="col-8 col-sm-4 col-md-2 mb-2">
                        <select name="month" id="post_month" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @if (isset($month) && (int) $month === $m) selected @endif>
                                    {{ \Carbon\Carbon::createFromFormat('!m', $m)->format('M') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <!-- Year -->
                    <div class="col-6 col-sm-4 col-md-2 mb-2">
                        <input type="number" name="year" id="post_year" class="form-control" placeholder="2025"
                            min="2023" value="{{ $year ?? '' }}">
                    </div>
                    <!-- "alle" label -->
                    <div class="col-6 col-sm-2 col-md-1 mb-2 d-flex align-items-center justify-content-center">
                        alle
                    </div>
                    <!-- Hour -->
                    <div class="col-6 col-sm-4 col-md-2 mb-2">
                        <input type="number" name="hour" id="post_hour" class="form-control" min="0"
                            max="23" placeholder="06" value="{{ $hour ?? '' }}">
                    </div>
                    <!-- Minute -->
                    <div class="col-6 col-sm-4 col-md-2 mb-2">
                        <input type="number" name="minute" id="post_minute" class="form-control" min="0"
                            max="59" placeholder="30" value="{{ $minute ?? '' }}">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="post_status">Status</label>
                <select id="post_status" name="status" class="form-control">
                    <option value="published" @if (isset($status) && $status === 'published') selected @endif>Published
                    </option>
                    <option value="draft" @if (isset($status) && $status === 'draft') selected @endif>Draft</option>
                </select>
            </div>

            <!-- Categories -->
            <div class="form-group">
                <label for="categories">Categorie</label>
                <div>
                    @if (isset($categories) && is_array($categories))
                        @foreach ($categories as $catId => $catName)
                            <label>
                                <input type="checkbox" name="categories[]" value="{{ $catId }}"
                                    @if (isset($selectedCategories) && in_array($catId, $selectedCategories)) checked @endif>
                                {{ $catName }}
                            </label><br>
                        @endforeach
                    @else
                        <!-- Fallback static list -->
                        <label>
                            <input type="checkbox" name="categories[]" value="1"> Archivio Sondaggi
                        </label><br>
                        <label>
                            <input type="checkbox" name="categories[]" value="2"> Featured
                        </label>
                    @endif
                </div>
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="post_tags">Tag</label>
                <textarea id="post_tags" name="tags" class="form-control" placeholder="Separa i tag con delle virgole">{{ $tags ?? '' }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary cancel-quick-edit"
                data-id="{{ $post->id }}">Cancel</button>
        </form>
    </td>
</tr>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // When the Quick Edit button is clicked, toggle the inline edit form row.
    $(document).on('click', '.quick-edit-btn', function() {
        var id = $(this).data('id');
        $('#quick-edit-row-' + id).toggle();
    });

    // Hide the form if the Cancel button is clicked.
    $(document).on('click', '.cancel-quick-edit', function() {
        var id = $(this).data('id');
        $('#quick-edit-row-' + id).hide();
    });
</script>
