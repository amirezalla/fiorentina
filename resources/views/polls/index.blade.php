<div class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sondaggi Esistenti</h1>
        {{-- Example: a "Crea" button if you want a link to create a new poll --}}
        <a href="{{ route('polls.create') }}" class="btn btn-primary">Crea</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Domanda</th>
                    <th>Numero Opzioni</th>
                    <th>Stato</th>
                    <th>Posizione</th>
                    <th>Data Scadenza</th>
                    <th style="width: 220px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($polls as $poll)
                    <tr>
                        <td class="align-middle">
                            {{ $poll->question }}
                        </td>
                        <td class="align-middle">
                            {{ $poll->options->count() }}
                        </td>
                        <td class="align-middle">
                            {{ $poll->active ? 'Attivo' : 'Inattivo' }}
                        </td>
                        <td class="align-middle">
                            {{-- Show the position field. You can adapt how you display these. --}}
                            @switch($poll->position)
                                @case('end')
                                    Alla fine
                                @break

                                @case('top')
                                    In alto
                                @break

                                @case('under_calendario')
                                    Sotto calendario
                                @break

                                @default
                                    {{ $poll->position }}
                            @endswitch
                        </td>
                        <td class="align-middle">
                            {{-- If expiry_date is not null, format it in dd/mm/yyyy. --}}
                            @if ($poll->expiry_date)
                                {{ \Carbon\Carbon::parse($poll->expiry_date)->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="align-middle">
                            <div class="d-flex flex-wrap gap-2">
                                {{-- Toggle Active/Inactive --}}
                                <a href="{{ route('polls.toggle', $poll->id) }}" class="btn btn-sm btn-secondary">
                                    Attiva/Disattiva
                                </a>

                                {{-- Export --}}
                                <a href="{{ route('polls.export', $poll->id) }}" class="btn btn-sm btn-success">
                                    Esporta
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('polls.edit', $poll->id) }}" class="btn btn-sm btn-primary">
                                    Modifica
                                </a>

                                {{-- Delete (inline form) --}}
                                <form action="{{ route('polls.destroy', $poll->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Sei sicuro di voler eliminare questo sondaggio?')">
                                        Elimina
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination links --}}
    <div class="mt-3">
        {{ $polls->links() }}
    </div>
</div>
