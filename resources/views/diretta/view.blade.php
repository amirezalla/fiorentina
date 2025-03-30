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

    // 1) After you decode the JSON into $commentariesJson...
    $commentaries = $commentariesJson;

    // 2) Sort the array with usort(), replicating your DB logic:
    usort($commentaries, function ($a, $b) {
        // Convert each comment_time into an integer that reflects minutes + extra time
        return getCommentTimeValue($a['comment_time'] ?? '') <=> getCommentTimeValue($b['comment_time'] ?? '');
    });

    /**
     * Convert a string like "45+2'" or "90+3'" into an integer minute count,
     * e.g. "45+2" => 47, "90+3" => 93, "60'" => 60, etc.
     */
    function getCommentTimeValue($commentTime)
    {
        if (empty($commentTime)) {
            return 0;
        }

        // Remove any trailing apostrophe, e.g. "45+2'" => "45+2"
        $commentTime = rtrim($commentTime, "'");

        // If there's a plus sign, split into main + added
    if (strpos($commentTime, '+') !== false) {
        [$main, $extra] = explode('+', $commentTime);
        return (int) $main + (int) $extra;
    }

    // If there's no plus sign, just cast to int
        return (int) $commentTime;
    }

    // Now $commentaries is sorted in ascending order
    // You can reverse it if you want descending, or keep it ascending

@endphp

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('diretta.includes.addDiretta', [
        'commentaries' => $commentaries, // Combined or from JSON only
    ])
@endsection
