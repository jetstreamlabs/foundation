<?php

namespace Serenity;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;
use ProtoneMedia\Splade\Facades\Splade;
use ProtoneMedia\Splade\Http\SpladeMiddleware;
use Serenity\Console\InstallCommand;
use Serenity\Contracts\FailedPasswordConfirmationInterface;
use Serenity\Contracts\FailedPasswordResetInterface;
use Serenity\Contracts\FailedPasswordResetLinkRequestInterface;
use Serenity\Contracts\FailedTwoFactorLoginInterface;
use Serenity\Contracts\LockoutInterface;
use Serenity\Contracts\LoginInterface;
use Serenity\Contracts\LogoutInterface;
use Serenity\Contracts\PasswordConfirmedInterface;
use Serenity\Contracts\PasswordResetInterface;
use Serenity\Contracts\PasswordUpdateInterface;
use Serenity\Contracts\ProfileInformationUpdatedInterface;
use Serenity\Contracts\RecoveryCodesGeneratedInterface;
use Serenity\Contracts\RegisterInterface;
use Serenity\Contracts\SuccessfulPasswordResetLinkRequestInterface;
use Serenity\Contracts\TwoFactorAuthenticationProviderInterface;
use Serenity\Contracts\TwoFactorConfirmedInterface;
use Serenity\Contracts\TwoFactorDisabledInterface;
use Serenity\Contracts\TwoFactorEnabledInterface;
use Serenity\Contracts\TwoFactorLoginInterface;
use Serenity\Contracts\VerifyEmailInterface;
use Serenity\Responders\FailedPasswordConfirmationResponder;
use Serenity\Responders\FailedPasswordResetLinkRequestResponder;
use Serenity\Responders\FailedPasswordResetResponder;
use Serenity\Responders\FailedTwoFactorLoginResponder;
use Serenity\Responders\LockoutResponder;
use Serenity\Responders\LoginResponder;
use Serenity\Responders\LogoutResponder;
use Serenity\Responders\PasswordConfirmedResponder;
use Serenity\Responders\PasswordResetResponder;
use Serenity\Responders\PasswordUpdateResponder;
use Serenity\Responders\ProfileInformationUpdatedResponder;
use Serenity\Responders\RecoveryCodesGeneratedResponder;
use Serenity\Responders\RegisterResponder;
use Serenity\Responders\SuccessfulPasswordResetLinkRequestResponder;
use Serenity\Responders\TwoFactorConfirmedResponder;
use Serenity\Responders\TwoFactorDisabledResponder;
use Serenity\Responders\TwoFactorEnabledResponder;
use Serenity\Responders\TwoFactorLoginResponder;
use Serenity\Responders\VerifyEmailResponder;
use Serenity\Routing\Discovery\Discover;
use Serenity\Routing\RemovableRoutesMixin;

class SerenityServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../../config/serenity.php',
      'serenity'
    );
  }

  public function boot()
  {
    Serenity::viewPrefix('auth.');

    Route::mixin(new RemovableRoutesMixin());

    $this->registerProviders();
    $this->registerMiddleware();
    $this->registerMacros();

    $this->configurePublishing();
    $this->configureRoutes();
    $this->configureCommands();

    $this->app->singleton(TwoFactorAuthenticationProviderInterface::class, function ($app) {
      return new TwoFactorAuthenticationProvider(
        $app->make(Google2FA::class),
        $app->make(Repository::class)
      );
    });

    $this->app->bind(StatefulGuard::class, function () {
      return Auth::guard(config('serenity.auth_guard', null));
    });

    RedirectResponse::macro('banner', function ($message) {
      return $this->with('flash', [
        'bannerStyle' => 'success',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('dangerBanner', function ($message) {
      return $this->with('flash', [
        'bannerStyle' => 'danger',
        'banner' => $message,
      ]);
    });

    $this->registerResponseBindings();
  }

  protected function registerMacros()
  {
    Builder::macro('scope', function ($scope) {
      return $scope->getQuery($this);
    });
  }

  protected function registerProviders()
  {
    $this->app->singleton('breadcrumb', function (Application $app) {
      return $app->make(\Serenity\Breadcrumbs::class);
    });
  }

  protected function configurePublishing()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__.'/../../stubs/config/serenity.php' => config_path('serenity.php'),
        __DIR__.'/../../stubs/config/app.php' => config_path('app.php'),
      ], 'serenity-config');
    }
  }

  protected function configureRoutes()
  {
    if (Serenity::$registersRoutes) {
      Route::group([
        'namespace' => 'Serenity\Actions',
        'domain' => config('serenity.domain', null),
        'prefix' => config('serenity.prefix'),
      ], function () {
        $this->loadRoutesFrom(__DIR__.'/../../routes/routes.php');
      });
    }

    if ($this->app->routesAreCached()) {
      return;
    }

    $this
        ->registerRoutesForActions()
        ->registerRoutesForViews();

    $this->callAfterResolving('laravel-splade', function () {
      if (app('router')->has('splade.confirmedPasswordStatus')) {
        Route::remove('GET', config('splade.confirm_password_route'));
        Route::get(config('splade.confirm_password_route'), \Serenity\Actions\Splade\ConfirmPassword\ShowAction::class)->name('splade.confirmedPasswordStatus');
      }
      if (app('router')->has('splade.confirmPassword')) {
        Route::remove('POST', config('splade.confirm_password_route'));
        Route::post(config('splade.confirm_password_route'), \Serenity\Actions\Splade\ConfirmPassword\StoreAction::class)->name('splade.confirmPassword');
      }
    });
  }

  /**
   * Configure the commands offered by the application.
   *
   * @return void
   */
  protected function configureCommands()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        InstallCommand::class,
      ]);
    }
  }

  protected function registerMiddleware()
  {
    $router = $this->app['router'];
    $router->pushMiddlewareToGroup('web', \Serenity\Middleware\MuteActions::class);
  }

  protected function registerResponseBindings()
  {
    $this->app->bind(FailedPasswordConfirmationInterface::class, FailedPasswordConfirmationResponder::class);
    $this->app->bind(FailedPasswordResetLinkRequestInterface::class, FailedPasswordResetLinkRequestResponder::class);
    $this->app->bind(FailedPasswordResetInterface::class, FailedPasswordResetResponder::class);
    $this->app->bind(FailedTwoFactorLoginInterface::class, FailedTwoFactorLoginResponder::class);
    $this->app->bind(LockoutInterface::class, LockoutResponder::class);
    $this->app->bind(LoginInterface::class, LoginResponder::class);
    $this->app->bind(LogoutInterface::class, LogoutResponder::class);
    $this->app->bind(PasswordConfirmedInterface::class, PasswordConfirmedResponder::class);
    $this->app->bind(PasswordResetInterface::class, PasswordResetResponder::class);
    $this->app->bind(PasswordUpdateInterface::class, PasswordUpdateResponder::class);
    $this->app->bind(ProfileInformationUpdatedInterface::class, ProfileInformationUpdatedResponder::class);
    $this->app->bind(RecoveryCodesGeneratedInterface::class, RecoveryCodesGeneratedResponder::class);
    $this->app->bind(RegisterInterface::class, RegisterResponder::class);
    $this->app->bind(SuccessfulPasswordResetLinkRequestInterface::class, SuccessfulPasswordResetLinkRequestResponder::class);
    $this->app->bind(TwoFactorConfirmedInterface::class, TwoFactorConfirmedResponder::class);
    $this->app->bind(TwoFactorDisabledInterface::class, TwoFactorDisabledResponder::class);
    $this->app->bind(TwoFactorEnabledInterface::class, TwoFactorEnabledResponder::class);
    $this->app->bind(TwoFactorLoginInterface::class, TwoFactorLoginResponder::class);
    $this->app->bind(VerifyEmailInterface::class, VerifyEmailResponder::class);

    SpladeMiddleware::afterOriginalResponse(function () {
      if (! session('flash.banner')) {
        return;
      }

      Splade::share('jetstreamBanner', function () {
        return [
          'banner' => session('flash.banner'),
          'bannerStyle' => session('flash.bannerStyle'),
        ];
      });
    });
  }

  public function registerRoutesForActions(): self
  {
    collect(config('serenity.action_directory'))
        ->each(
          fn (string $directory) => Discover::actions()->in($directory)
        );

    return $this;
  }

  public function registerRoutesForViews(): self
  {
    collect(config('serenity.responder_directory'))
        ->each(function (array|string $directories, int|string $prefix) {
          if (is_numeric($prefix)) {
            $prefix = '';
          }

          $directories = Arr::wrap($directories);

          foreach ($directories as $directory) {
            Route::prefix($prefix)->group(function () use ($directory) {
              Discover::views()->in($directory);
            });
          }
        });

    return $this;
  }

  protected function rebindLaravelDefaults(): void
  {
    $this->app->bind(
      'command.controller.make',
      'command.action.make'
    );

    $this->app->bind(
      'command.model.make',
      'command.entity.make'
    );
  }

    public function provides()
    {
      //return array_merge(array_values($this->devCommands));
    }
}
