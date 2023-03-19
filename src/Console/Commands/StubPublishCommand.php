<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\PublishingStubs;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'stub:publish')]
class StubPublishCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'stub:publish
    {--existing : Publish and overwrite only the files that have already been published}
    {--force : Overwrite any existing files}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Publish all stubs that are available for customization';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    if (! is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
      (new Filesystem)->makeDirectory($stubsPath);
    }

    $stubs = [
      __DIR__.'/stubs/action.api.stub' => 'action.api.stub',
      __DIR__.'/stubs/action.plain.stub' => 'action.plain.stub',
      __DIR__.'/stubs/action.stub' => 'action.stub',
      __DIR__.'/stubs/cast.inbound.stub' => 'cast.inbound.stub',
      __DIR__.'/stubs/cast.stub' => 'cast.stub',
      __DIR__.'/stubs/channel.stub' => 'channel.stub',
      __DIR__.'/stubs/console.stub' => 'console.stub',
      __DIR__.'/stubs/event.stub' => 'event.stub',
      __DIR__.'/stubs/exception-render-report.stub' => 'exception-render-report.stub',
      __DIR__.'/stubs/exception-render.stub' => 'exception-render.stub',
      __DIR__.'/stubs/exception-report.stub' => 'exception-report.stub',
      __DIR__.'/stubs/exception.stub' => 'exception.stub',
      __DIR__.'/stubs/factory.stub' => 'factory.stub',
      __DIR__.'/stubs/job.queued.stub' => 'job.queued.stub',
      __DIR__.'/stubs/job.stub' => 'job.stub',
      __DIR__.'/stubs/listener-duck.stub' => 'listener-duck.stub',
      __DIR__.'/stubs/listener-queued-duck.stub' => 'listener-queued-duck.stub',
      __DIR__.'/stubs/listener-queued.stub' => 'listener-queued.stub',
      __DIR__.'/stubs/listener.stub' => 'listener.stub',
      __DIR__.'/stubs/mail.stub' => 'mail.stub',
      __DIR__.'/stubs/maintenance-mode.stub' => 'maintenance-mode.stub',
      __DIR__.'/stubs/markdown-mail.stub' => 'markdown-mail.stub',
      __DIR__.'/stubs/markdown-notification.stub' => 'markdown-notification.stub',
      __DIR__.'/stubs/markdown.stub' => 'markdown.stub',
      __DIR__.'/stubs/middleware.stub' => 'middleware.stub',
      __DIR__.'/stubs/migration.create.stub' => 'migration.create.stub',
      __DIR__.'/stubs/migration.stub' => 'migration.stub',
      __DIR__.'/stubs/migration.update.stub' => 'migration.update.stub',
      __DIR__.'/stubs/model.morph-pivot.stub' => 'model.morph-pivot.stub',
      __DIR__.'/stubs/model.pivot.stub' => 'model.pivot.stub',
      __DIR__.'/stubs/model.stub' => 'model.stub',
      __DIR__.'/stubs/notification.stub' => 'notification.stub',
      __DIR__.'/stubs/observer.plain.stub' => 'observer.plain.stub',
      __DIR__.'/stubs/observer.stub' => 'observer.stub',
      __DIR__.'/stubs/page.stub' => 'page.stub',
      __DIR__.'/stubs/pest.stub' => 'pest.stub',
      __DIR__.'/stubs/pest.unit.stub' => 'pest.unit.stub',
      __DIR__.'/stubs/policy.plain.stub' => 'policy.plain.stub',
      __DIR__.'/stubs/policy.stub' => 'policy.stub',
      __DIR__.'/stubs/provider.stub' => 'provider.stub',
      __DIR__.'/stubs/repository-interface.stub' => 'repository-interface.stub',
      __DIR__.'/stubs/repository.stub' => 'repository.stub',
      __DIR__.'/stubs/request.stub' => 'request.stub',
      __DIR__.'/stubs/resource.stub' => 'resource.stub',
      __DIR__.'/stubs/resource-collection.stub' => 'resource-collection.stub',
      __DIR__.'/stubs/responder-interface.stub' => 'responder-interface.stub',
      __DIR__.'/stubs/responder.stub' => 'responder.stub',
      __DIR__.'/stubs/routes.stub' => 'routes.stub',
      __DIR__.'/stubs/rule.implicit.stub' => 'rule.implicit.stub',
      __DIR__.'/stubs/rule.stub' => 'rule.stub',
      __DIR__.'/stubs/scope.stub' => 'scope.stub',
      __DIR__.'/stubs/seeder.stub' => 'seeder.stub',
      __DIR__.'/stubs/service.api.stub' => 'service.api.stub',
      __DIR__.'/stubs/service.stub' => 'service.stub',
      __DIR__.'/stubs/test.stub' => 'test.stub',
      __DIR__.'/stubs/test.unit.stub' => 'test.unit.stub',
    ];

    $this->laravel['events']->dispatch($event = new PublishingStubs($stubs));

    foreach ($event->stubs as $from => $to) {
      $to = $stubsPath.DIRECTORY_SEPARATOR.ltrim($to, DIRECTORY_SEPARATOR);

      if ((! $this->option('existing') && (! file_exists($to) || $this->option('force')))
          || ($this->option('existing') && file_exists($to))) {
        file_put_contents($to, file_get_contents($from));
      }
    }

    $this->components->info('Stubs published successfully.');
  }
}
