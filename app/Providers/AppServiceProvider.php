<?php

namespace App\Providers;

use App\Repositories\BetRepository;
use App\Repositories\ConfigRepository;
use App\Repositories\GameRepository;
use App\Repositories\IBetRepository;
use App\Repositories\IConfigRepository;
use App\Repositories\IGameRepository;
use App\Repositories\ILobbyRepository;
use App\Repositories\IPartnerRepository;
use App\Repositories\IReferralRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\IUserRepository;
use App\Repositories\LobbyRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\ReferralRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\CompiledRouteCollection;
use App\Vendor\newCompiledRouteCollection;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias(CompiledRouteCollection::class, newCompiledRouteCollection::class);

        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);

        $this->app->bind(ILobbyRepository::class, LobbyRepository::class);
        $this->app->bind(IBetRepository::class, BetRepository::class);
        $this->app->bind(IGameRepository::class, GameRepository::class);
        $this->app->bind(IReferralRepository::class, ReferralRepository::class);
        $this->app->bind(ITransactionRepository::class, TransactionRepository::class);
        $this->app->bind(IConfigRepository::class, ConfigRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IPartnerRepository::class, PartnerRepository::class);
    }
}
