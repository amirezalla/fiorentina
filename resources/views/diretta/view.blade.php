@php
    use App\Models\Calendario;
    use App\Models\MatchCommentary;
    if ($matchId) {
        $match = Calendario::where('match_id', $matchId)->first();
        $commentaries = MatchCommentary::where('match_id', $matchId)
            ->where(function ($query) {
                $query->whereNotNull('comment_time')->orWhereNotNull('comment_class')->orWhereNotNull('comment_text');
            })
            ->orderByRaw(
                "
        CAST(SUBSTRING_INDEX(comment_time, \"'\", 1) AS UNSIGNED) + 
        IF(LOCATE('+', comment_time) > 0, 
            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(comment_time, \"'\", 1), '+', -1) AS UNSIGNED), 
            0
        )
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
