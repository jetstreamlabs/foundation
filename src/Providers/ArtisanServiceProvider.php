<?php

namespace Serenity\Providers;

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Illuminate\Cache\Console\ForgetCommand as CacheForgetCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\ScheduleClearCacheCommand;
use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\ScheduleTestCommand;
use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Illuminate\Console\Signals;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Database\Console\MonitorCommand as DatabaseMonitorCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Console\ShowCommand;
use Illuminate\Database\Console\ShowModelCommand;
use Illuminate\Database\Console\TableCommand as DatabaseTableCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Console\ChannelListCommand;
use Illuminate\Foundation\Console\ClearCompiledCommand;
use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Foundation\Console\ConfigClearCommand;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\EnvironmentCommand;
use Illuminate\Foundation\Console\EnvironmentDecryptCommand;
use Illuminate\Foundation\Console\EnvironmentEncryptCommand;
use Illuminate\Foundation\Console\EventCacheCommand;
use Illuminate\Foundation\Console\EventClearCommand;
use Illuminate\Foundation\Console\EventListCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\LangPublishCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Foundation\Console\OptimizeCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Foundation\Console\RouteClearCommand;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\Console\StorageLinkCommand;
use Illuminate\Foundation\Console\UpCommand;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Foundation\Console\ViewCacheCommand;
use Illuminate\Foundation\Console\ViewClearCommand;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Queue\Console\BatchesTableCommand;
use Illuminate\Queue\Console\ClearCommand as QueueClearCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\FlushFailedCommand as FlushFailedQueueCommand;
use Illuminate\Queue\Console\ForgetFailedCommand as ForgetFailedQueueCommand;
use Illuminate\Queue\Console\ListenCommand as QueueListenCommand;
use Illuminate\Queue\Console\ListFailedCommand as ListFailedQueueCommand;
use Illuminate\Queue\Console\MonitorCommand as QueueMonitorCommand;
use Illuminate\Queue\Console\PruneBatchesCommand as QueuePruneBatchesCommand;
use Illuminate\Queue\Console\PruneFailedJobsCommand as QueuePruneFailedJobsCommand;
use Illuminate\Queue\Console\RestartCommand as QueueRestartCommand;
use Illuminate\Queue\Console\RetryBatchCommand as QueueRetryBatchCommand;
use Illuminate\Queue\Console\RetryCommand as QueueRetryCommand;
use Illuminate\Queue\Console\TableCommand;
use Illuminate\Queue\Console\WorkCommand as QueueWorkCommand;
use Illuminate\Session\Console\SessionTableCommand;
use Illuminate\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{
  /**
   * The commands to be registered.
   *
   * @var array
   */
  protected $commands = [
    'About' => AboutCommand::class,
    'CacheClear' => CacheClearCommand::class,
    'CacheForget' => CacheForgetCommand::class,
    'ClearCompiled' => ClearCompiledCommand::class,
    'ClearResets' => ClearResetsCommand::class,
    'ConfigCache' => ConfigCacheCommand::class,
    'ConfigClear' => ConfigClearCommand::class,
    'Db' => DbCommand::class,
    'DbMonitor' => DatabaseMonitorCommand::class,
    'DbPrune' => PruneCommand::class,
    'DbShow' => ShowCommand::class,
    'DbTable' => DatabaseTableCommand::class,
    'DbWipe' => WipeCommand::class,
    'Down' => DownCommand::class,
    'Environment' => EnvironmentCommand::class,
    'EnvironmentDecrypt' => EnvironmentDecryptCommand::class,
    'EnvironmentEncrypt' => EnvironmentEncryptCommand::class,
    'EventCache' => EventCacheCommand::class,
    'EventClear' => EventClearCommand::class,
    'EventList' => EventListCommand::class,
    'KeyGenerate' => KeyGenerateCommand::class,
    'Optimize' => OptimizeCommand::class,
    'OptimizeClear' => OptimizeClearCommand::class,
    'PackageDiscover' => PackageDiscoverCommand::class,
    'PruneStaleTagsCommand' => PruneStaleTagsCommand::class,
    'QueueClear' => QueueClearCommand::class,
    'QueueFailed' => ListFailedQueueCommand::class,
    'QueueFlush' => FlushFailedQueueCommand::class,
    'QueueForget' => ForgetFailedQueueCommand::class,
    'QueueListen' => QueueListenCommand::class,
    'QueueMonitor' => QueueMonitorCommand::class,
    'QueuePruneBatches' => QueuePruneBatchesCommand::class,
    'QueuePruneFailedJobs' => QueuePruneFailedJobsCommand::class,
    'QueueRestart' => QueueRestartCommand::class,
    'QueueRetry' => QueueRetryCommand::class,
    'QueueRetryBatch' => QueueRetryBatchCommand::class,
    'QueueWork' => QueueWorkCommand::class,
    'RouteCache' => RouteCacheCommand::class,
    'RouteClear' => RouteClearCommand::class,
    'RouteList' => RouteListCommand::class,
    'SchemaDump' => DumpCommand::class,
    'Seed' => SeedCommand::class,
    'ScheduleFinish' => ScheduleFinishCommand::class,
    'ScheduleList' => ScheduleListCommand::class,
    'ScheduleRun' => ScheduleRunCommand::class,
    'ScheduleClearCache' => ScheduleClearCacheCommand::class,
    'ScheduleTest' => ScheduleTestCommand::class,
    'ScheduleWork' => ScheduleWorkCommand::class,
    'ShowModel' => ShowModelCommand::class,
    'StorageLink' => StorageLinkCommand::class,
    'Up' => UpCommand::class,
    'ViewCache' => ViewCacheCommand::class,
    'ViewClear' => ViewClearCommand::class,
  ];

  /**
   * The commands to be registered.
   *
   * @var array
   */
  protected $devCommands = [
    'CacheTable' => CacheTableCommand::class,
    'ChannelList' => ChannelListCommand::class,
    'LangPublish' => LangPublishCommand::class,
    'NotificationTable' => NotificationTableCommand::class,
    'QueueFailedTable' => FailedTableCommand::class,
    'QueueTable' => TableCommand::class,
    'QueueBatchesTable' => BatchesTableCommand::class,
    'SeederMake' => SeederMakeCommand::class,
    'SessionTable' => SessionTableCommand::class,
    'Serve' => ServeCommand::class,
    'VendorPublish' => VendorPublishCommand::class,
  ];

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->registerCommands(array_merge(
      $this->commands,
      $this->devCommands
    ));

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
  protected function registerAboutCommand()
  {
    $this->app->singleton(AboutCommand::class, function ($app) {
      return new AboutCommand($app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerCacheClearCommand()
  {
    $this->app->singleton(CacheClearCommand::class, function ($app) {
      return new CacheClearCommand($app['cache'], $app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerCacheForgetCommand()
  {
    $this->app->singleton(CacheForgetCommand::class, function ($app) {
      return new CacheForgetCommand($app['cache']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerCacheTableCommand()
  {
    $this->app->singleton(CacheTableCommand::class, function ($app) {
      return new CacheTableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerConfigCacheCommand()
  {
    $this->app->singleton(ConfigCacheCommand::class, function ($app) {
      return new ConfigCacheCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerConfigClearCommand()
  {
    $this->app->singleton(ConfigClearCommand::class, function ($app) {
      return new ConfigClearCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerEventClearCommand()
  {
    $this->app->singleton(EventClearCommand::class, function ($app) {
      return new EventClearCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerNotificationTableCommand()
  {
    $this->app->singleton(NotificationTableCommand::class, function ($app) {
      return new NotificationTableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueForgetCommand()
  {
    $this->app->singleton(ForgetFailedQueueCommand::class);
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueListenCommand()
  {
    $this->app->singleton(QueueListenCommand::class, function ($app) {
      return new QueueListenCommand($app['queue.listener']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueMonitorCommand()
  {
    $this->app->singleton(QueueMonitorCommand::class, function ($app) {
      return new QueueMonitorCommand($app['queue'], $app['events']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueuePruneBatchesCommand()
  {
    $this->app->singleton(QueuePruneBatchesCommand::class, function () {
      return new QueuePruneBatchesCommand;
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueuePruneFailedJobsCommand()
  {
    $this->app->singleton(QueuePruneFailedJobsCommand::class, function () {
      return new QueuePruneFailedJobsCommand;
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueRestartCommand()
  {
    $this->app->singleton(QueueRestartCommand::class, function ($app) {
      return new QueueRestartCommand($app['cache.store']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueWorkCommand()
  {
    $this->app->singleton(QueueWorkCommand::class, function ($app) {
      return new QueueWorkCommand($app['queue.worker'], $app['cache.store']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueFailedTableCommand()
  {
    $this->app->singleton(FailedTableCommand::class, function ($app) {
      return new FailedTableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueTableCommand()
  {
    $this->app->singleton(TableCommand::class, function ($app) {
      return new TableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerQueueBatchesTableCommand()
  {
    $this->app->singleton(BatchesTableCommand::class, function ($app) {
      return new BatchesTableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerSeederMakeCommand()
  {
    $this->app->singleton(SeederMakeCommand::class, function ($app) {
      return new SeederMakeCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerSessionTableCommand()
  {
    $this->app->singleton(SessionTableCommand::class, function ($app) {
      return new SessionTableCommand($app['files'], $app['composer']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRouteCacheCommand()
  {
    $this->app->singleton(RouteCacheCommand::class, function ($app) {
      return new RouteCacheCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRouteClearCommand()
  {
    $this->app->singleton(RouteClearCommand::class, function ($app) {
      return new RouteClearCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerRouteListCommand()
  {
    $this->app->singleton(RouteListCommand::class, function ($app) {
      return new RouteListCommand($app['router']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerSeedCommand()
  {
    $this->app->singleton(SeedCommand::class, function ($app) {
      return new SeedCommand($app['db']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerVendorPublishCommand()
  {
    $this->app->singleton(VendorPublishCommand::class, function ($app) {
      return new VendorPublishCommand($app['files']);
    });
  }

  /**
   * Register the command.
   *
   * @return void
   */
  protected function registerViewClearCommand()
  {
    $this->app->singleton(ViewClearCommand::class, function ($app) {
      return new ViewClearCommand($app['files']);
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array_merge(array_values($this->commands), array_values($this->devCommands));
  }
}
