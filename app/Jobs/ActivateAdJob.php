<?php

namespace App\Jobs;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActivateAdJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $ad;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Ad $ad
     */
    public function __construct(Ad $ad)
    {
        // It is a good idea to re-retrieve the model from the database in handle() so the job works on the latest data.
        $this->ad = $ad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Retrieve the latest ad instance.
        $ad = Ad::find($this->ad->id);
        if ($ad) {
            // If the start_date is now or in the past, set ad status to active.
            if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($ad->start_date))) {
                $ad->status = 1;
                $ad->save();
            }
        }
    }
}
