<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class StoreMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageData;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
        $this->messageData['member'] = Member::find($messageData['user_id']); // Attach member data
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = 'chat/messages_' . $this->messageData['match_id'] . '.json';
        $messages = [];

        if (Storage::exists($filePath)) {
            $messages = json_decode(Storage::get($filePath), true);
        }

        $messages[] = $this->messageData;

        Storage::put($filePath, json_encode($messages));
    }
}