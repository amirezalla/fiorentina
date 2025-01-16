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
        foreach (range(0, $max_page - 1) as $i) {
            $o = $i * 100;
            ImportUserFromWpUsersDatabase::dispatch($o);
        }
    }
}
