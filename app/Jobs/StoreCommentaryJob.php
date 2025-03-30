<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class StoreCommentaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commentaryData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $commentaryData)
    {
        // This is the single new commentary or an array of new commentaries
        $this->commentaryData = $commentaryData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $matchId = $this->commentaryData['match_id'];

        // The path could be commentary/commentary_XXXX.json
        $filePath = 'commentary/commentary_' . $matchId . '.json';

        // Load existing commentary from the JSON file
        $existingCommentaries = [];
        if (Storage::exists($filePath)) {
            $existingCommentaries = json_decode(Storage::get($filePath), true);
        }

        // Append the new commentary
        // If $this->commentaryData is an array of items, you’d merge it
        // But here we assume a single item for simplicity
        $existingCommentaries[] = $this->commentaryData;

        // Save back to Wasabi
        Storage::put($filePath, json_encode($existingCommentaries));
    }
}
