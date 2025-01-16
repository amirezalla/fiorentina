<?php

namespace App\Classes;

use App\Jobs\ImportUserFromWpUsersDatabase;
use Botble\ACL\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportPwDatabase
{
    /**
     * @return void
     */
    public function importUsers()
    {
        $usersCount = DB::connection('mysql2')->table("frntn_users")->count();
        $max_page = ceil($usersCount / 100);
        dump($usersCount);
        foreach (range(0, $max_page - 1) as $i) {
            $o = $i * 100;
            $users = DB::connection('mysql2')
                ->table("frntn_users")
                ->skip($o)
                ->limit(5)
                ->get()
                ->map(fn($i) => json_decode(json_encode($i), true))
                ->toArray();
            foreach ($users as $user) {
                $firstName = trim(Str::before($user['display_name'], " "));
                $lastName = trim(Str::after($user['display_name'], " "));
                User::unguard();
                User::query()->create([
                    'id' => $user['ID'],
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
            dd($users);
//            ImportUserFromWpUsersDatabase::dispatch($i * 100);
        }
    }
}
