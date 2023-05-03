<?php

namespace Serenity\Providers;

use App\Domain\Middleware\HandleInertiaRequests;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
use Serenity\Console\InstallCommand;
use Serenity\Contracts\ContractMapper;
use Serenity\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderInterface;
use Serenity\Markdown\Contracts\Frontmatter;
use Serenity\Middleware\MuteActions;
use Serenity\Middleware\ShareInertiaData;
use Serenity\Routing\Finder\Find;
use Serenity\Serenity;

class ZenServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../Config/serenity.php', 'serenity'
    );
  }

  public function boot()
  {
    Serenity::viewPrefix('auth.');

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

    $this->registerProviders();
    $this->registerMacros();
    $this->configurePublishing();
    $this->configureRoutes();
    $this->configureCommands();
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
      \Serenity\Contracts\SerenityManager::class,
      \Serenity\Foundation\SerenityManager::class
    );

    $this->app->bind(
      \Serenity\Contracts\Breadcrumbs::class,
      \Serenity\Foundation\Breadcrumbs::class
    );

    $this->app->bind(
      \Serenity\Markdown\Contracts\BlockParser::class,
      \Serenity\Markdown\Parsers\BlockParser::class
    );

    $this->app->bind(
      \Serenity\Markdown\Contracts\MarkdownParser::class,
      \Serenity\Markdown\Parsers\MarkdownParser::class
    );

    $this->app->bind(
      \Serenity\Markdown\Contracts\VoidParser::class,
      \Serenity\Markdown\Parsers\VoidParser::class
    );

    $this->app->bind(
      \Serenity\Markdown\Contracts\YamlParser::class,
      \Serenity\Markdown\Parsers\YamlParser::class
    );

    $this->app->bind(
      \Serenity\Markdown\Contracts\Frontmatter::class,
      \Serenity\Markdown\Frontmatter::class
    );

    $this->app->bind(ContractMapper::class, function (Container $app) {
      return $app->make(\Serenity\Support\ContractMapper::class);
    });

    $this->app->singleton(SerenityManager::class, function (Container $app) {
      return $app->make(\Serenity\Contracts\SerenityManager::class);
    });

    $this->app->singleton('breadcrumbs', function (Container $app) {
      $breadcrumbs = $app->make(\Serenity\Contracts\Breadcrumbs::class);
      $breadcrumbs->add(env('APP_NAME'), route('home'));

      return $breadcrumbs;
    });

    $this->app->singleton(Frontmatter::class, function (Container $app) {
      return $app->make(\Serenity\Markdown\Frontmatter::class);
    });
  }

  protected function configurePublishing()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__.'/../../config/serenity.php' => config_path('serenity.php'),
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
        $this->loadRoutesFrom(__DIR__.'/../Routing/routes/routes.php');
      });
    }

    if ($this->app->routesAreCached()) {
      return;
    }

    $this->registerRoutesForActions();
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

  /**
   * Auto-bind all of our responders to their interfaces.
   *
   * @return void
   */
  protected function registerResponseBindings(): void
  {
    $mapper = $this->app->make(ContractMapper::class);
    $mapper
      ->setConcretePath('Responders')
      ->setInterfacePath('Contracts');

    $mapper->map();
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
        fn (string $directory) => Find::actions()->in($directory)
      );

    return $this;
  }

  protected function rebindLaravelDefaults(): void
  {
    $this->app->bind(
      'command.controller.make',
      'command.action.make'
    );
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides(): array
  {
    return [];
  }
}
