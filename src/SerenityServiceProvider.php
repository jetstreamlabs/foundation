<?php

namespace Serenity;

use App\Domain\Middleware\HandleInertiaRequests;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Container\Container;
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
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use PragmaRX\Google2FA\Google2FA;
use Serenity\Console\InstallCommand;
use Serenity\Contracts\ContractMapper;
use Serenity\Contracts\DocumentationRepository;
use Serenity\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderInterface;
use Serenity\Markdown\Compiler\CommonMarkCompiler;
use Serenity\Markdown\Directive\CommonMarkDirective;
use Serenity\Markdown\Directive\DirectiveInterface;
use Serenity\Markdown\Highlighters\HighlightCodeExtension;
use Serenity\Markdown\MarkdownRenderer;
use Serenity\Markdown\Renderers\AnchorHeadingRenderer;
use Serenity\Middleware\MuteActions;
use Serenity\Middleware\ShareInertiaData;
use Serenity\Routing\Discovery\Discover;
use Serenity\Routing\Finder\Find;

class SerenityServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../config/serenity.php', 'serenity'
    );

    $this->mergeConfigFrom(
      __DIR__.'/../config/markdown.php', 'markdown'
    );

    $this->mergeConfigFrom(
      __DIR__.'/../config/docs.php', 'docs'
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

    $this->app->bind(ContractMapper::class, function (Application $app) {
      return $app->make(\Serenity\Support\ContractMapper::class);
    });

    $this->app->singleton(DocumentationRepository::class, function (Application $app) {
      return $app->make(\Serenity\Entities\DocumentationRepository::class);
    });

    $this->registerEnvironment();
    $this->registerMarkdown();
    $this->registerCompiler();
    $this->registerDirective();

    $this->app->singleton(ParseHandlerr::class, function (Container $app) {
      return $app->make(\Serenity\Markdown\Renderers\ParseHandler::class);
    });
  }

  protected function configurePublishing()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__.'/../config/serenity.php' => config_path('serenity.php'),
        __DIR__.'/../config/markdown.php' => config_path('markdown.php'),
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

  /**
   * Register the environment class.
   *
   * @return void
   */
  private function registerEnvironment(): void
  {
    $this->app->singleton('markdown.environment', function (Container $app): Environment {
      $config = $app->config->get('markdown');

      $environment = new Environment(Arr::except($config, ['extensions']));

      foreach ((array) Arr::get($config, 'extensions') as $extension) {
        $environment->addExtension($app->make($extension));
      }

      $environment->addExtension(new CommonMarkCoreExtension());
      $environment->addExtension(new TableExtension());
      $environment->addExtension(new HighlightCodeExtension());
      $environment->addExtension(new FrontMatterExtension());
      $environment->addExtension(new GithubFlavoredMarkdownExtension());
      $environment->addExtension(new TableOfContentsExtension());
      $environment->addExtension(new HeadingPermalinkExtension());

      $environment->addRenderer(Heading::class, new AnchorHeadingRenderer());

      return $environment;
    });

    $this->app->alias('markdown.environment', Environment::class);
    $this->app->alias('markdown.environment', EnvironmentInterface::class);
    $this->app->alias('markdown.environment', EnvironmentBuilderInterface::class);
  }

  /**
   * Register the markdowm class.
   *
   * @return void
   */
  private function registerMarkdown(): void
  {
    $this->app->singleton('markdown.converter', function (Container $app): MarkdownConverter {
      $environment = $app['markdown.environment'];

      return new MarkdownConverter($environment);
    });

    $this->app->alias('markdown.converter', MarkdownConverter::class);
    $this->app->alias('markdown.converter', ConverterInterface::class);
  }

  /**
   * Register the markdown compiler class.
   *
   * @return void
   */
  private function registerCompiler(): void
  {
    $this->app->singleton('markdown.compiler', function (Container $app): CommonMarkCompiler {
      $converter = $app['markdown.converter'];
      $files = $app['files'];
      $storagePath = $app->config->get('view.compiled');

      return new CommonMarkCompiler($converter, $files, $storagePath);
    });

    $this->app->alias('markdown.compiler', CommonMarkCompiler::class);
  }

  /**
   * Register the markdown directive class.
   *
   * @return void
   */
  private function registerDirective(): void
  {
    $this->app->singleton('markdown.directive', function (Container $app): CommonMarkDirective {
      $converter = $app['markdown.converter'];

      return new CommonMarkDirective($converter);
    });

    $this->app->alias('markdown.directive', CommonMarkDirective::class);
    $this->app->alias('markdown.directive', DirectiveInterface::class);
  }

  public function registerRoutesForActions(): self
  {
    collect(config('serenity.action_directory'))
      ->each(
        fn (string $directory) => Find::actions()->in($directory)
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
            Find::docs()->in($directory);
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
      return [
        'markdown.environment',
        'markdown.converter',
        'markdown.compiler',
        'markdown.directive',
      ];
    }
}
