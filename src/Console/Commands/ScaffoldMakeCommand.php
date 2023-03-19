<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:scaffold')]
class ScaffoldMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:scaffold';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Scaffold all classes for a namespace.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Namespace';

  /**
   * Execute the console command.
   *
   * @return bool|null
   */
  public function handle()
  {
    dd($this->option('all'));

    if ($this->option('basic')) {
      $this->createBasicActions();
      $this->createModel();
    }

    if ($this->option('api')) {
      $this->createApiActions();
      $this->createModel();
    }

    if ($this->option('all')) {
      $this->createBasicActions();
      $this->createModel();
      $this->createRepository();
      $this->createObserver();
    }

    if ($this->option('repo')) {
      $this->createRepository();
    }

    if ($this->option('observer')) {
      $this->createObserver();
    }
  }

  /**
   * Create a set of standard actions, responders and pages.
   *
   * @return void
   */
  protected function createBasicActions(): void
  {
    $files = ['Index', 'Create', 'Show', 'Store', 'Edit', 'Update', 'Delete', 'Restore', 'Destroy'];

    $namespace = Str::studly(class_basename($this->argument('name')));

    if ($this->argument('visibility')) {
      $visibility = match (mb_strtolower($this->argument('visibility'))) {
        'g' => 'Public',
        'm' => 'Protected',
        'a' => 'Private',
        'n' => ''
      };

      $namespace = $visibility.DIRECTORY_SEPARATOR.$namespace;
    }

    if ($this->option('model')) {
      $model = Str::singular($this->option('model'));
    } else {
      $model = Str::singular($this->argument('name'));
    }

    foreach ($files as $file) {
      $action = $namespace
        .DIRECTORY_SEPARATOR
        .$file;

      $this->call('make:action', [
        'name' => "{$action}Action",
        '--model' => $model,
      ]);

      $this->createResponder($file);
      $this->createPage($file);
    }

    $this->createService($model);
  }

  /**
   * Generate Api actions for the application.
   *
   * @return void
   */
  public function createApiActions()
  {
    $files = ['List', 'Store', 'Update', 'Delete', 'Restore', 'Destroy'];

    $namespace = Str::studly(class_basename($this->argument('name')));

    if ($this->argument('visibility')) {
      $visibility = match (mb_strtolower($this->argument('visibility'))) {
        'g' => 'Public',
        'm' => 'Protected',
        'a' => 'Private',
        'n' => '',
      };

      $namespace = $visibility.DIRECTORY_SEPARATOR.$namespace;
    }

    if ($this->option('model')) {
      $model = Str::singular($this->option('model'));
    } else {
      $model = Str::singular($this->argument('name'));
    }

    foreach ($files as $file) {
      $action = $namespace
        .DIRECTORY_SEPARATOR
        .$file;

      $this->call('make:action', [
        'name' => "{$action}Action",
        '--model' => $model,
        '--api' => true,
      ]);
    }

    $this->createService($model);
  }

  /**
   * Create a new responder and interface.
   *
   * @param  string  $file
   * @return void
   */
  protected function createResponder(string $file): void
  {
    $namespace = Str::studly(class_basename($this->argument('name')));

    $responder = $namespace.DIRECTORY_SEPARATOR.$file;

    $this->call('make:responder', [
      'name' => "{$responder}Responder",
    ]);

    $this->call('make:responder-contract', [
      'name' => "{$responder}Responder",
    ]);
  }

  /**
   * Create a new Inertia view page.
   *
   * @param  string  $file
   * @return void
   */
  protected function createPage(string $file): void
  {
    $files = ['Index', 'Create', 'Show', 'Edit'];

    $namespace = Str::studly(class_basename($this->argument('name')));

    if (in_array($file, $files)) {
      $page = $namespace.DIRECTORY_SEPARATOR.$file;

      $this->call('make:page', [
        'name' => "{$page}",
      ]);
    }
  }

  /**
   * Create a new model, factory, migration, policy.
   *
   * @return void
   */
  public function createModel(): void
  {
    $name = $this->qualifyModel($this->argument('name'));

    if ($this->alreadyExists($name)) {
      $this->info('This model already exists, skipping');
    } else {
      $this->call('make:model', [
        'name' => Str::singular($this->argument('name')),
        '--migration' => true,
        '--factory' => true,
        '--policy' => true,
      ]);
    }
  }

  /**
   * Create a service class for the application.
   *
   * @param  string $model
   * @return void
   */
  protected function createService(string $model): void
  {
    $service = Str::singular($this->argument('name'));

    $this->call('make:service', [
      'name' => "{$service}Service",
      '--model' => $model,
      '--api' => $this->option('api') ?? false,
    ]);
  }

  protected function createRepository()
  {
    $name = Str::singular($this->argument('name'));

    $model = $this->option('model')
      ? $this->option('model')
      : $name;

    $this->call('make:repository', [
      'name' => "{$name}Repository",
      'model' => $model,
    ]);
  }

  public function createObserver()
  {
    $name = Str::singular($this->argument('name'));

    $model = $this->option('model')
      ? $this->option('model')
      : $name;

    $this->call('make:observer', [
      'name' => "{$name}Observer",
      '--model' => $model,
    ]);
  }

  public function getStub()
  {
    // No stub, this command only runs other generators.
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The '.strtolower($this->type).' to create for the app'],
      ['visibility', InputArgument::REQUIRED, 'Set the visibility for the namespace (G: Public, M: Protected, A: Private)'],
    ];
  }

  /**
   * Prompt for missing input arguments using the returned questions.
   *
   * @return array
   */
  protected function promptForMissingArgumentsUsing()
  {
    return [
      'name' => 'The '.strtolower($this->type).' to create for the app?',
      'visibility' => 'Set the visibility for the namespace (G: Public, M: Protected, A: Private)',
    ];
  }

  protected function getOptions()
  {
    return [
      ['all', null, InputOption::VALUE_NONE, 'Create all classes for a namespace.'],
      ['api', null, InputOption::VALUE_NONE, 'Create all classes for an Api namespace.'],
      ['basic', null, InputOption::VALUE_NONE, 'Include actions, responders, model and service.'],
      ['model', 'm', InputOption::VALUE_OPTIONAL, 'Include a model.'],
      ['service', null, InputOption::VALUE_NONE, 'Include a service'],
      ['repo', null, InputOption::VALUE_NONE, 'Include a repository.'],
      ['observer', null, InputOption::VALUE_NONE, 'Include an observer.'],
    ];
  }
}
