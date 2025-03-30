@php
    use Illuminate\Support\Str;
@endphp
<style>
    .edit-box {
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        border-radius: 5px;
    }
</style>
<div class="container mt-3">
    @foreach ($commentaries as $comment)
        <div class="commentary-row {{ $comment['comment_class'] }} {{ $comment['is_important'] ? 'important' : '' }}{{ $comment['is_bold'] ? 'comment-is-bold' : '' }}"
            data-id="{{ $comment['id'] }}">
            <div class="comment-time" style="flex: 0.5">{{ $comment['comment_time'] }}</div>
            <div style="flex: 0.5">
                @if (Str::contains(request()->url(), '/diretta/view'))
                    <a style="margin-right: 5px" href="/delete-commentary?id={{ $comment->id }}"><i
                            class="text-danger fa-solid fa-trash"></i></a>
                    <a style="margin-right: 5px" href="#" onclick="toggleEditBox({{ $comment['id'] }},event)"><i
                            class="text-white fa-solid fa-pen-to-square"></i></i></a>
                @endif
            </div>
            <div class="comment-icon"></div>
            <div class="comment-text {{ $comment['is_bold'] ? 'comment-bold' : '' }}">{{ $comment['comment_text'] }}
            </div>
        </div>

        <!-- Hidden edit box -->
        <div id="edit-box-{{ $comment['id'] }}" class="edit-box" style="display: none; margin-top: 10px;">
            <form action="/update-commentary" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $comment['id'] }}">
                <textarea name="comment_text" class="form-control" rows="3">{{ $comment['comment_text'] }}</textarea>
                <div class="form-check mt-2">
                    <input type="checkbox" name="is_important" class="form-check-input"
                        id="is_important_{{ $comment['id'] }}" {{ $comment['is_important'] ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_important_{{ $comment['id'] }}">Important</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_bold" class="form-check-input" id="is_bold_{{ $comment['id'] }}"
                        {{ $comment['is_bold'] ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_bold_{{ $comment['id'] }}">Bold</label>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Save</button>
                <button type="button" class="btn btn-secondary mt-2"
                    onclick="toggleEditBox({{ $comment['id'] }})">Cancel</button>
            </form>
        </div>
    @endforeach
</div>

<script>
    function toggleEditBox(id, event) {
        event.preventDefault();

        const editBox = document.getElementById(`edit-box-${id}`);
        const row = document.querySelector(`.commentary-row[data-id="${id}"]`);

        if (editBox.style.display === 'none' || editBox.style.display === '') {
            editBox.style.display = 'block';

            // Scroll to the row
            if (row) {
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        } else {
            editBox.style.display = 'none';
        }
    }
</script>

