@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
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
                                    <a href="{{ route('polls.edit', $poll->id) }}" class="btn btn-sm btn-primary"
                                        style="padding-right: 0">
                                        <svg class="icon" data-bs-toggle="tooltip" data-bs-title="Modifica"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                            </path>
                                            <path d="M16 5l3 3"></path>
                                        </svg>
                                    </a>

                                    {{-- Delete (inline form) --}}
                                    <form action="{{ route('polls.destroy', $poll->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding-right: 0"
                                            onclick="return confirm('Sei sicuro di voler eliminare questo sondaggio?')">
                                            <svg class="icon" data-bs-toggle="tooltip" data-bs-title="Elimina"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 7l16 0"></path>
                                                <path d="M10 11l0 6"></path>
                                                <path d="M14 11l0 6"></path>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                            </svg>
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
@endsection
