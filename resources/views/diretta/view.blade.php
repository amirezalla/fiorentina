@php
    use App\Models\Calendario;
    use App\Models\MatchCommentary;
    if ($matchId) {
        // Build the file path in your Wasabi bucket:
        $filePath = "commentary/commentary_{$matchId}.json";

        // Check if the file exists
        if (Storage::exists($filePath)) {
            // Get file contents
            $contents = Storage::get($filePath);

            // Decode JSON into an array
            $commentaries = json_decode($contents, true);

            // If decoding failed or returned null, set an empty array
            if (!is_array($commentaries)) {
                $commentaries = [];
            }
        }
    }
@endphp
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('diretta.includes.addDiretta', [
        'commentaries' => $commentaries,
    ])
@endsection
