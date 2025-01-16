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
            dump($i * 100);
//            ImportUserFromWpUsersDatabase::dispatch($i * 100);
        }
    }
}
