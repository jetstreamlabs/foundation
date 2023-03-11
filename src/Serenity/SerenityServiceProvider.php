<?php

namespace Serenity;

use App\Domain\Middleware\HandleInertiaRequests;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
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
use Serenity\Middleware\MuteActions;
use Serenity\Middleware\ShareInertiaData;
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

    $this->registerProviders();
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
      /** @var \Illuminate\Http\RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'success',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('dangerBanner', function ($message) {
      /** @var \Illuminate\Http\RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'danger',
        'banner' => $message,
      ]);
    });

    $this->registerResponseBindings();
    $this->bootInertia();
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
  }

  protected function bootInertia()
  {
    $kernel = $this->app->make(Kernel::class);

    $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);
    //$kernel->appendMiddlewareToGroup('web', MuteActions::class);
    $kernel->appendToMiddlewarePriority(ShareInertiaData::class);

    if (class_exists(HandleInertiaRequests::class)) {
      $kernel->appendToMiddlewarePriority(HandleInertiaRequests::class);
    }

    Serenity::loginView(function () {
      return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
      ]);
    });

    Serenity::requestPasswordResetLinkView(function () {
      return Inertia::render('Auth/ForgotPassword', [
        'status' => session('status'),
      ]);
    });

    Serenity::resetPasswordView(function (Request $request) {
      return Inertia::render('Auth/ResetPassword', [
        'email' => $request->input('email'),
        'token' => $request->route('token'),
      ]);
    });

    Serenity::registerView(function () {
      return Inertia::render('Auth/Register');
    });

    Serenity::verifyEmailView(function () {
      return Inertia::render('Auth/VerifyEmail', [
        'status' => session('status'),
      ]);
    });

    Serenity::twoFactorChallengeView(function () {
      return Inertia::render('Auth/TwoFactorChallenge');
    });

    Serenity::confirmPasswordView(function () {
      return Inertia::render('Auth/ConfirmPassword');
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
    collect(config('serenity.docs_directory'))
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
