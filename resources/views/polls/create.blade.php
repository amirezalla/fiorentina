@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        <h1>Crea un Nuovo Sondaggio</h1>
        <form method="POST" action="{{ route('polls.storepoll') }}">
            @csrf
            <div class="form-group">
                <label for="question">Domanda</label>
                <input type="text" class="form-control" id="question" name="question" required>
            </div>
            <div class="form-group" id="options-container">
                <label for="options">Opzioni</label>
                <input type="text" class="form-control mb-2" name="options[]" required>
                <input type="text" class="form-control mb-2" name="options[]" required>
            </div>
            <div class="row mb-3 p-2">
                <button type="button" class="col-6 btn btn-secondary mb-3" onclick="addOption()">Aggiungi un'altra opzione</button>
            </div>

            <div class="form-group">
                <label for="min_choices">L'utente deve votare almeno per:</label>
                <input type="number" class="form-control" id="min_choices" name="min_choices" min="1" value="1" required>
            </div>

            <div class="row p-2">
                <button type="submit" class="col-12 btn btn-primary">Crea Sondaggio</button>
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
