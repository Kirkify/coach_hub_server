<?php

namespace App\Providers;

use App\Http\Grant\EmailOnlyGrant;
use App\Providers\Traits\TokenExpiryTrait;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\UserRepository;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends ServiceProvider
{
    use TokenExpiryTrait;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'place-orders' => 'Place orders',
            'check-status' => 'Check order status',
        ]);

        Passport::routes(function ($router) {
            $router->forAccessTokens();
            // $router->forPersonalAccessTokens();
            $router->forTransientTokens();
        });

        Passport::tokensExpireIn(now()->addMinutes(config('auth.tokens.expiry.access_token')));
        Passport::refreshTokensExpireIn(now()->addMinutes(config('auth.tokens.expiry.refresh_token')));
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->afterResolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $grant = $this->makeGrant();
            $server->enableGrantType($grant, Passport::tokensExpireIn());
        });
    }

    private function makeGrant() {
        $grant = new EmailOnlyGrant(
            $this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
