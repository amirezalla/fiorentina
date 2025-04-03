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
