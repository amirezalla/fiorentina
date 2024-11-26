@php
    use App\Models\Calendario;
    use App\Models\MatchCommentary;
    if ($matchId) {
        $match = Calendario::where('match_id', $matchId)->first();
        $commentaries = MatchCommentary::where('match_id', $matchId)
            ->orderByRaw(
                "
        CAST(SUBSTRING_INDEX(comment_time, '+', 1) AS UNSIGNED) ASC,
        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(comment_time, '+', -1), \"'\", 1) AS UNSIGNED) ASC,
        created_at ASC
    ",
            )
            ->get();
    }
@endphp
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('diretta.includes.addDiretta', [
        'commentaries' => $commentaries,
    ])
@endsection
