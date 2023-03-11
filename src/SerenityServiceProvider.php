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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
use Serenity\Console\InstallCommand;
use Serenity\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderInterface;
use Serenity\Middleware\MuteActions;
use Serenity\Middleware\ShareInertiaData;
use Serenity\Routing\Discovery\Discover;

class SerenityServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../config/serenity.php',
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
    $this->app->bind(
      \Serenity\Contracts\Breadcrumbs::class,
      \Serenity\Breadcrumbs::class
    );

    $this->app->singleton('breadcrumbs', function (Application $app) {
      $breadcrumbs = $app->make(\Serenity\Contracts\Breadcrumbs::class);
      $breadcrumbs->add('Dashboard', route('dashboard'));

      return $breadcrumbs;
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
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
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
    $directory = __DIR__.'/Responders';

    if (is_dir($directory)) {
      $files = File::allFiles($directory);

      $bindings = collect($files)->map(function ($file) {
        $path = $file->getRelativePath();

        if (! empty($path)) {
          $path = '\\'.$path;
        }

        $fileClass = rtrim($file, '.'.$file->getExtension());
        $contract = 'Serenity\\Contracts'.$path.'\\'.basename($fileClass);
        $concrete = 'Serenity\\'.str_replace('/', '\\', ltrim($fileClass, __DIR__));

        if ($this->hasInterface($concrete, $contract)) {
          return [
            'contract' => $contract,
            'concrete' => $concrete,
          ];
        }
      }, collect([]));
    }

    if (! $bindings->isEmpty()) {
      $bindings->each(function ($binding) {
        if (! is_null($binding)) {
          $this->app->bind($binding['contract'], $binding['concrete']);
        }
      });
    }
  }

  protected function bootInertia()
  {
    $kernel = $this->app->make(Kernel::class);

    $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);
    $kernel->appendMiddlewareToGroup('web', MuteActions::class);
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

  /**
   * Determine if the repository has an interface to bind.
   *
   * @param  string  $concrete
   * @param  string  $contract
   * @return bool
   */
  protected function hasInterface(string $concrete, string $contract): bool
  {
    $reflected = new \ReflectionClass($concrete);
    $interfaces = $reflected->getInterfaces();

    return array_key_exists($contract, $interfaces);
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
