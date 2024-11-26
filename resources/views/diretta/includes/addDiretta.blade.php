@php
    use App\Models\MatchCommentary;

    $uniqueCommentClasses = MatchCommentary::select('comment_class')->distinct()->pluck('comment_class');

@endphp
@if (session('success'))
    <div class="alert alert-success">
        {!! session('success') !!}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form action="" method="POST" class="p-3">
    @csrf
    <div class="row mb-3">
        <!-- Time Input -->
        <div class="col-md-2">
            <label for="time" class="form-label">Time</label>
            <input type="number" id="time" name="time" class="form-control" required>
        </div>

        <!-- Tipo di Event Select -->
        <div class="col-md-4">
            <label for="tipo_event" class="form-label">Tipo di Event</label>
            <select id="tipo_event" name="tipo_event" class="form-select" required>
                <option value="" disabled selected>Select Event</option>
                @foreach ($uniqueCommentClasses as $comment_class)
                    <option value="{{ $comment_class }}" class="icon-{{ $comment_class }}">
                        {{ $comment_class }}
                    </option>
                @endforeach
                <!-- Add more options as needed -->
            </select>
        </div>
    </div>

    <!-- Comment Text Area -->
    <div class="mb-3">
        <label for="comment_text" class="form-label">Comment Text</label>
        <textarea id="comment_text" name="comment_text" rows="4" class="form-control" required></textarea>
    </div>

    <!-- Radio Buttons -->
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="style" id="bold" value="bold" required>
            <label class="form-check-label" for="bold">Bold</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="style" id="important" value="important" required>
            <label class="form-check-label" for="important">Important</label>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
<style>
    .commentary-container {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
    }

    .commentary-row {
        background-color: #212529f0;
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #444;
        padding-left: 10px;
        border-left: 5px solid #212529f0;


    }

    .comment-time {
        flex: 1;
        color: #9e9e9e;
        font-weight: bold;
    }

    .comment-text {
        flex: 4;
        color: #f0f6fc;
        padding-right: 10px;
    }

    .comment-icon {
        flex: 0.5;
        font-size: 20px;
        color: #f0f6fc;
        margin-right: 15px;
    }

    /* Specific classes for different event types */
    .whistle .comment-icon::before {
        content: '\1F3C1';
        /* Whistle emoji */
    }

    .y-card .comment-icon::before {
        content: '\1F7E1';
        /* Yellow card emoji */
    }

    .soccer-ball .comment-icon::before {
        content: '\26BD';
        /* Soccer ball emoji */
    }

    .corner .comment-icon::before {
        content: '\1F4CB';
        /* Corner emoji */
    }

    .substitution .comment-icon::before {
        content: '\1F504';
        /* Substitution emoji */
    }

    /* Bold text for important comments */
    .comment-bold {
        font-weight: bold;
    }

    /* Styling for important events */
    .important {
        background-color: #1d2025;
        border-left: 5px solid #d83a56;

    }

    .stage {
        border-radius: 21px;
        background-color: #1f2b36;
        color: white;
        padding: 16px 20px;
    }

    .incident {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #444;
        padding: 5px 0;
    }

    .incident-time {
        color: yellow;
    }

    .incident-detail span {
        font-weight: bold;
        color: #f9f9f9;
    }

    .incident-detail span.GOAL {
        color: yellow;
    }
</style>
@include('ads.includes.commentary', ['commentaries' => $commentaries])
