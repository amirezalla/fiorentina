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

    /**
     * Create a new job instance.
     */
    public function __construct($skipValue)
    {
        //
        $this->skipValue = $skipValue;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = DB::connection('mysql2')
            ->table("frntn_users")
            ->skip($this->skipValue)
            ->limit(100)
            ->get()
            ->map(fn($i) => json_decode(json_encode($i), true))
            ->toArray();
        foreach ($users as $user) {
            $firstName = trim(Str::before($user['display_name'], " "));
            $lastName = trim(Str::after($user['display_name'], " "));
            User::unguard();
            User::query()->create([
                'email' => $user['user_email'],
                'username' => $user['user_nicename'],
                'password' => $user['user_pass'],
                'first_name' => strlen($firstName) ? $firstName : null,
                'last_name' => strlen($lastName) ? $lastName : null,
                'created_at' => $user['user_registered'],
                'email_verified_at' => now(),
            ]);
            User::reguard();
        }
    }
}
