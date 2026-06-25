<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\ContactRepository;

use App\Services\Interfaces\ContactServiceInterface;
use App\Services\ContactService;

use App\Services\Interfaces\AuthServiceInterface;
use App\Services\AuthService;

use App\Repositories\Interfaces\NewsletterSubscriberRepositoryInterface;
use App\Repositories\NewsletterSubscriberRepository;

use App\Services\Interfaces\NewsletterSubscriberServiceInterface;
use App\Services\NewsletterSubscriberService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ContactServiceInterface::class, ContactService::class);

        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        $this->app->bind(NewsletterSubscriberRepositoryInterface::class, NewsletterSubscriberRepository::class);
        $this->app->bind(NewsletterSubscriberServiceInterface::class, NewsletterSubscriberService::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
