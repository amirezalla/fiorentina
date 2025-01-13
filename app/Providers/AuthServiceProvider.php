<?php

namespace App\Providers;
use Illuminate\Support\Facades\Hash;
use Ayesh\WP_PasswordHash\PasswordHash;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Hash::extend('wordpress', function () {
            return new class {
                public function check($value, $hashedValue)
                {
                    return CheckPassword::check($value, $hashedValue);
                }
    
                public function make($value, array $options = [])
                {
                    throw new \Exception('Password creation is not implemented for WordPress hashing.');
                }
    
                public function needsRehash($hashedValue, array $options = [])
                {
                    return false; // WordPress passwords don't need rehashing
                }
            };
        });
    }
}
