@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        <h1>Edit Poll</h1>
        <form method="POST" action="{{ route('polls.update', $poll->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" class="form-control" id="question" name="question" value="{{ old('question', $poll->question) }}" required>
            </div>

            <div class="form-group" id="options-container">
                <label for="options">Options</label>
                @foreach($poll->options as $option)
                    <input type="text" class="form-control mb-2" name="options[]" value="{{ old('options.' . $loop->index, $option->option) }}" required>
                @endforeach
            </div>

            <div class="row mb-3 p-2">
                <button type="button" class="col-6 btn btn-secondary mb-3" onclick="addOption()">Add another option</button>
            </div>

            <div class="row p-2">
                <button type="submit" class="col-12 btn btn-primary">Update Poll</button>
            </div>

        </form>
    </div>

    <script>
        function addOption() {
            const container = document.getElementById('options-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'options[]';
            input.required = true;
            input.classList.add('form-control', 'mb-2');
            container.appendChild(input);
        }
    </script>
@endsection
