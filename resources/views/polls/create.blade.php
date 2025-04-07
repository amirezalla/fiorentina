@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        <h1>Create a New Poll</h1>
        <form method="POST" action="{{ route('polls.storepoll') }}">
            @csrf
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" class="form-control" id="question" name="question" required>
            </div>
            <div class="form-group" id="options-container">
                <label for="options">Options</label>
                <input type="text" class="form-control mb-2" name="options[]" required>
                <input type="text" class="form-control mb-2" name="options[]" required>
            </div>
            <div class="row mb-3 p-2">
                <button type="button" class="col-6 btn btn-secondary mb-3" onclick="addOption()">Add another option</button>
            </div>

            <div class="form-group">
                <label for="min_choices">User must vote for at least this many choice(s):</label>
                <input type="number" class="form-control" id="min_choices" name="min_choices" min="1" value="1" required>
            </div>

            <div class="row p-2">
                <button type="submit" class="col-12 btn btn-primary">Create Poll</button>
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
