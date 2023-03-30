<?php

namespace Serenity\Providers;

use Illuminate\Console\Signals;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Serenity\Console\Commands\ActionMakeCommand;
use Serenity\Console\Commands\CastMakeCommand;
use Serenity\Console\Commands\ChannelMakeCommand;
use Serenity\Console\Commands\ConsoleMakeCommand;
use Serenity\Console\Commands\DocsCommand;
use Serenity\Console\Commands\EventGenerateCommand;
use Serenity\Console\Commands\EventMakeCommand;
use Serenity\Console\Commands\ExceptionMakeCommand;
use Serenity\Console\Commands\FactoryMakeCommand;
use Serenity\Console\Commands\JobMakeCommand;
use Serenity\Console\Commands\ListenerMakeCommand;
use Serenity\Console\Commands\MailMakeCommand;
use Serenity\Console\Commands\MiddlewareMakeCommand;
use Serenity\Console\Commands\ModelMakeCommand;
use Serenity\Console\Commands\ModelRepositoryMakeCommand;
use Serenity\Console\Commands\NotificationMakeCommand;
use Serenity\Console\Commands\ObserverMakeCommand;
use Serenity\Console\Commands\PageMakeCommand;
use Serenity\Console\Commands\PolicyMakeCommand;
use Serenity\Console\Commands\ProviderMakeCommand;
use Serenity\Console\Commands\RepositoryInterfaceMakeCommand;
use Serenity\Console\Commands\RepositoryMakeCommand;
use Serenity\Console\Commands\RequestMakeCommand;
use Serenity\Console\Commands\ResourceMakeCommand;
use Serenity\Console\Commands\ResponderInterfaceMakeCommand;
use Serenity\Console\Commands\ResponderMakeCommand;
use Serenity\Console\Commands\RuleMakeCommand;
use Serenity\Console\Commands\ScaffoldMakeCommand;
use Serenity\Console\Commands\ScopeMakeCommand;
use Serenity\Console\Commands\ServiceMakeCommand;
use Serenity\Console\Commands\StubPublishCommand;
use Serenity\Console\Commands\TestMakeCommand;

class GeneratorServiceProvider extends ServiceProvider implements DeferrableProvider
{
  /**
   * The commands to be registered.
   *
   * @var array
   */
  protected $devCommands = [
    'ActionMake' => ActionMakeCommand::class,
    'CastMake' => CastMakeCommand::class,
    'ChannelMake' => ChannelMakeCommand::class,
    'ConsoleMake' => ConsoleMakeCommand::class,
    'Docs' => DocsCommand::class,
    'EventGenerate' => EventGenerateCommand::class,
    'EventMake' => EventMakeCommand::class,
    'ExceptionMake' => ExceptionMakeCommand::class,
    'FactoryMake' => FactoryMakeCommand::class,
    'JobMake' => JobMakeCommand::class,
    'ListenerMake' => ListenerMakeCommand::class,
    'MailMake' => MailMakeCommand::class,
    'MiddlewareMake' => MiddlewareMakeCommand::class,
    'ModelMake' => ModelMakeCommand::class,
    'ModelRepositoryMake' => ModelRepositoryMakeCommand::class,
    'NotificationMake' => NotificationMakeCommand::class,
    'ObserverMake' => ObserverMakeCommand::class,
    'PageMake' => PageMakeCommand::class,
    'PolicyMake' => PolicyMakeCommand::class,
    'ProviderMake' => ProviderMakeCommand::class,
    'RepositoryMake' => RepositoryMakeCommand::class,
    'RepositoryInterfaceMake' => RepositoryInterfaceMakeCommand::class,
    'RequestMake' => RequestMakeCommand::class,
    'ResourceMake' => ResourceMakeCommand::class,
    'ResponderMake' => ResponderMakeCommand::class,
    'ResponderInterfaceMake' => ResponderInterfaceMakeCommand::class,
    'RuleMake' => RuleMakeCommand::class,
    'ServiceMake' => ServiceMakeCommand::class,
    'ScaffoldMake' => ScaffoldMakeCommand::class,
    'ScopeMake' => ScopeMakeCommand::class,
    'StubPublish' => StubPublishCommand::class,
    'TestMake' => TestMakeCommand::class,
  ];

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->registerCommands($this->devCommands);

    Signals::resolveAvailabilityUsing(function () {
      return $this->app->runningInConsole()
          && ! $this->app->runningUnitTests()
          && extension_loaded('pcntl');
    });
  }

  /**
   * Register the given commands.
   *
   * @param  array  $commands
   * @return void
   */
  protected function registerCommands(array $commands)
  {
    foreach ($commands as $commandName => $command) {
      $method = "register{$commandName}Command";

      if (method_exists($this, $method)) {
        $this->{$method}();
      } else {
        $this->app->singleton($command);
      }
    }

    $this->commands(array_values($commands));
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerCastMakeCommand()
  {
    $this->app->singleton(CastMakeCommand::class, function ($app) {
      return new CastMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerChannelMakeCommand()
  {
    $this->app->singleton(ChannelMakeCommand::class, function ($app) {
      return new ChannelMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerConsoleMakeCommand()
  {
    $this->app->singleton(ConsoleMakeCommand::class, function ($app) {
      return new ConsoleMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerActionMakeCommand()
  {
    $this->app->singleton(ActionMakeCommand::class, function ($app) {
      return new ActionMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerEventMakeCommand()
  {
    $this->app->singleton(EventMakeCommand::class, function ($app) {
      return new EventMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerExceptionMakeCommand()
  {
    $this->app->singleton(ExceptionMakeCommand::class, function ($app) {
      return new ExceptionMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerFactoryMakeCommand()
  {
    $this->app->singleton(FactoryMakeCommand::class, function ($app) {
      return new FactoryMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerJobMakeCommand()
  {
    $this->app->singleton(JobMakeCommand::class, function ($app) {
      return new JobMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerListenerMakeCommand()
  {
    $this->app->singleton(ListenerMakeCommand::class, function ($app) {
      return new ListenerMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerMailMakeCommand()
  {
    $this->app->singleton(MailMakeCommand::class, function ($app) {
      return new MailMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerMiddlewareMakeCommand()
  {
    $this->app->singleton(MiddlewareMakeCommand::class, function ($app) {
      return new MiddlewareMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerModelMakeCommand()
  {
    $this->app->singleton(ModelMakeCommand::class, function ($app) {
      return new ModelMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerModelRepositoryMakeCommand()
  {
    $this->app->singleton(ModelRepositoryMakeCommand::class, function ($app) {
      return new ModelRepositoryMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerNotificationMakeCommand()
  {
    $this->app->singleton(NotificationMakeCommand::class, function ($app) {
      return new NotificationMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerObserverMakeCommand()
  {
    $this->app->singleton(ObserverMakeCommand::class, function ($app) {
      return new ObserverMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerPageMakeCommand()
  {
    $this->app->singleton(PageMakeCommand::class, function ($app) {
      return new PageMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerPolicyMakeCommand()
  {
    $this->app->singleton(PolicyMakeCommand::class, function ($app) {
      return new PolicyMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerProviderMakeCommand()
  {
    $this->app->singleton(ProviderMakeCommand::class, function ($app) {
      return new ProviderMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRepositoryMakeCommand()
  {
    $this->app->singleton(RepositoryMakeCommand::class, function ($app) {
      return new RepositoryMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRepositoryInterfaceMakeCommand()
  {
    $this->app->singleton(RepositoryInterfaceMakeCommand::class, function ($app) {
      return new RepositoryInterfaceMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRequestMakeCommand()
  {
    $this->app->singleton(RequestMakeCommand::class, function ($app) {
      return new RequestMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerResourceMakeCommand()
  {
    $this->app->singleton(ResourceMakeCommand::class, function ($app) {
      return new ResourceMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerResponderMakeCommand()
  {
    $this->app->singleton(ResponderMakeCommand::class, function ($app) {
      return new ResponderMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerResponderInterfaceMakeCommand()
  {
    $this->app->singleton(ResponderInterfaceMakeCommand::class, function ($app) {
      return new ResponderInterfaceMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRuleMakeCommand()
  {
    $this->app->singleton(RuleMakeCommand::class, function ($app) {
      return new RuleMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerScaffoldMakeCommand()
  {
    $this->app->singleton(ScaffoldMakeCommand::class, function ($app) {
      return new ScaffoldMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerScopeMakeCommand()
  {
    $this->app->singleton(ScopeMakeCommand::class, function ($app) {
      return new ScopeMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerServiceMakeCommand()
  {
    $this->app->singleton(ServiceMakeCommand::class, function ($app) {
      return new ServiceMakeCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerTestMakeCommand()
  {
    $this->app->singleton(TestMakeCommand::class, function ($app) {
      return new TestMakeCommand($app['files']);
    });
  }

  public function provides()
  {
    return array_values($this->devCommands);
  }
}
