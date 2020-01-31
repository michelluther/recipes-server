<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        
        $this->app['auth']->viaRequest('api', function ($request) {
            $authenticationHeader = $request->header('Authorization');
            if(0 === stripos($authenticationHeader, 'basic ')){
                $exploded = explode(':', base64_decode(substr($authenticationHeader, 6)), 2);
                if (2 == \count($exploded)) {
                    list($uname, $pw) = $exploded;
                }
            }
            $user = User::where('username', $uname)->first();
            if ($user->api_token == $request->input('api_token') && Hash::check($pw, $user->password)) {
                return new User(["username" => $uname ]);
            } else {
                return null;
            }
        });
    }
}
