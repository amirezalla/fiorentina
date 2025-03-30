@php
    use App\Models\Calendario;
    use App\Models\MatchCommentary;
    use Illuminate\Support\Facades\Storage;

    $commentariesDb = [];
    $commentariesJson = [];

    if ($matchId) {
        // 1) Fetch from DB as you did before (if you still want that logic)
        $commentariesDb = MatchCommentary::where('match_id', $matchId)
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
            ->get()
            ->toArray();

        // 2) Fetch from the JSON in Wasabi
        $filePath = "commentary/commentary_{$matchId}.json";
        if (Storage::exists($filePath)) {
            $contents = Storage::get($filePath);
            $commentariesJson = json_decode($contents, true) ?? [];
        }
    }

    // 3) Decide how to merge (depends on your data structure)
    // For example, if you ALWAYS want the JSON data, you might do:
    $commentaries = $commentariesJson;

    // Or you can merge them, if that's relevant:
    // $commentaries = array_merge($commentariesDb, $commentariesJson);

@endphp

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('diretta.includes.addDiretta', [
        'commentaries' => $commentaries, // Combined or from JSON only
    ])
@endsection
