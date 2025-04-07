@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {{-- We'll use a row with two columns: left for main form fields, right for the sidebar --}}
    <form id="pollForm" method="POST" action="{{ route('polls.storepoll') }}" class="row">
        @csrf

        {{-- LEFT COLUMN --}}
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">Dettagli Principali</h4>

                    {{-- Question --}}
                    <div class="mb-3">
                        <label for="question" class="form-label">Domanda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="question" name="question" required>
                    </div>

                    {{-- Options --}}
                    <div class="mb-3" id="options-container">
                        <label class="form-label">Opzioni (almeno 2) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="options[]" required>
                        <input type="text" class="form-control mb-2" name="options[]" required>
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" onclick="addOption()">Aggiungi opzione</button>

                    {{-- Min Choices --}}
                    <div class="mb-3">
                        <label for="min_choices" class="form-label">Min. scelte <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="min_choices" name="min_choices" min="1"
                            value="1" required>
                    </div>

                    {{-- (Optional) Additional fields, like a description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione (opzionale)</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (SIDEBAR) --}}
        <div class="col-md-4">
            {{-- Pubblica box --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Pubblica</strong>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 mb-3">
                        {{-- Main save button --}}
                        <button type="submit" class="btn btn-primary" name="action" value="save">
                            Salva
                        </button>
                        {{-- Save & exit button (optional) --}}
                        <button type="submit" class="btn btn-secondary" name="action" value="save_exit">
                            Salva e Esci
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stato --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Stato</strong>
                </div>
                <div class="card-body">
                    <select class="form-select" name="status" required>
                        <option value="pubblicato">Pubblicato</option>
                        <option value="bozza">Bozza</option>
                    </select>
                </div>
            </div>

            {{-- Posizionamento --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Posizionamento</strong>
                </div>
                <div class="card-body">
                    <select name="position" id="position" class="form-select" required>
                        <option value="end">Alla fine</option>
                        <option value="top">In alto</option>
                        <option value="under_calendario">Sotto il calendario</option>
                    </select>
                </div>
            </div>

            {{-- Data di scadenza --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Data di scadenza</strong>
                </div>
                <div class="card-body">
                    <input type="date" class="form-control" name="expiry_date" id="expiry_date">
                </div>
            </div>
        </div>
    </form>

    {{-- Minimal JS for adding options dynamically --}}
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
