<?php

namespace App\Classes;

use App\Jobs\ImportUserFromWpUsersDatabase;
use Illuminate\Support\Facades\DB;

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
                ->limit(100)
                ->get()
                ->map(fn($i) => json_decode(json_encode($i), true))
                ->toArray();
            dd($users);
//            ImportUserFromWpUsersDatabase::dispatch($i * 100);
        }
    }
}
