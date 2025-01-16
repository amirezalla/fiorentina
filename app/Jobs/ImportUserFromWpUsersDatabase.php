<?php

namespace App\Jobs;

use Botble\ACL\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportUserFromWpUsersDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $skipValue;
    private $limitValue;

    /**
     * Create a new job instance.
     */
    public function __construct($skipValue, $limitValue)
    {
        //
        $this->skipValue = $skipValue;
        $this->limitValue = $limitValue;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = DB::connection('mysql2')
            ->table("frntn_users")
            ->skip($this->skipValue)
            ->limit($this->limitValue)
            ->get()
            ->map(fn($i) => json_decode(json_encode($i), true))
            ->toArray();
        foreach ($users as $user) {
            $firstName = trim(Str::before($user['display_name'], " "));
            $lastName = trim(Str::after($user['display_name'], " "));
            $user = new User();
            $user->timestamps = false;
            $user->email = $user['user_email'];
            $user->username = $user['user_nicename'];
            $user->password = $user['user_pass'];
            $user->first_name = strlen($firstName) ? $firstName : null;
            $user->last_name = strlen($lastName) ? $lastName : null;
            $user->created_at = now()->subYears(5);
            $user->save();
        }
    }
}
