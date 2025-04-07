@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <h1>Sondaggi Esistenti</h1>
        <table class="table">
            <thead>
            <tr>
                <th>Domanda</th>
                <th>Numero Opzioni</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($polls as $poll)
                <tr>
                    <td>{{ $poll->question }}</td>
                    <td>{{ $poll->options->count() }}</td>
                    <td>{{ $poll->active ? 'Attivo' : 'Inattivo' }}</td>
                    <td>
                        <a href="{{ route('polls.toggle', $poll->id) }}" class="btn btn-sm btn-secondary">Attiva/Disattiva</a>
                        <a href="{{ route('polls.export', $poll->id) }}" class="btn btn-sm btn-success">Esporta</a>
                        <a href="{{ route('polls.edit', $poll->id) }}" class="btn btn-sm btn-primary">Modifica</a>
                        <form action="{{ route('polls.destroy', $poll->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Elimina</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $polls->links() }}
    </div>
@endsection
