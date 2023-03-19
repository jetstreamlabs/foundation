<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:model')]
class ModelMakeCommand extends GeneratorCommand
{
  use CreatesMatchingTest;
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:model';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new Eloquent model class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Model';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    if (parent::handle() === false && ! $this->option('force')) {
      return false;
    }

    if ($this->option('all')) {
      $this->input->setOption('factory', true);
      $this->input->setOption('seed', true);
      $this->input->setOption('migration', true);
      $this->input->setOption('policy', true);
    }

    if ($this->option('factory')) {
      $this->createFactory();
    }

    if ($this->option('migration')) {
      $this->createMigration();
    }

    if ($this->option('seed')) {
      $this->createSeeder();
    }

    if ($this->option('policy')) {
      $this->createPolicy();
    }
  }

  /**
   * Create a model factory for the model.
   *
   * @return void
   */
  protected function createFactory()
  {
    $factory = Str::studly($this->argument('name'));

    $this->call('make:factory', [
      'name' => "{$factory}Factory",
      '--model' => $this->qualifyClass($this->getNameInput()),
    ]);
  }

  /**
   * Create a migration file for the model.
   *
   * @return void
   */
  protected function createMigration()
  {
    $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

    if ($this->option('pivot')) {
      $table = Str::singular($table);
    }

    $this->call('make:migration', [
      'name' => "create_{$table}_table",
      '--create' => $table,
      '--fullpath' => true,
    ]);
  }

  /**
   * Create a seeder file for the model.
   *
   * @return void
   */
  protected function createSeeder()
  {
    $seeder = Str::studly(class_basename($this->argument('name')));

    $this->call('make:seeder', [
      'name' => "{$seeder}Seeder",
    ]);
  }

  /**
   * Create a policy file for the model.
   *
   * @return void
   */
  protected function createPolicy()
  {
    $policy = Str::studly(class_basename($this->argument('name')));

    $this->call('make:policy', [
      'name' => "{$policy}Policy",
      '--model' => $this->qualifyClass($this->getNameInput()),
    ]);
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    if ($this->option('pivot')) {
      return $this->resolveStubPath('/stubs/model.pivot.stub');
    }

    if ($this->option('morph-pivot')) {
      return $this->resolveStubPath('/stubs/model.morph-pivot.stub');
    }

    return $this->resolveStubPath('/stubs/model.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return is_dir(app_path('Domain/Models')) ? $rootNamespace.'\\Domain\\Models' : $rootNamespace.'\\Models';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
      ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
      ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
      ['morph-pivot', null, InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom polymorphic intermediate table model'],
      ['policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'],
      ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
      ['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
    ];
  }

  /**
   * Interact further with the user if they were prompted for missing arguments.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
  protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
  {
    if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
      return;
    }

    collect($this->components->choice('Would you like any of the following?', [
      'none',
      'all',
      'factory',
      'migration',
      'policy',
      'seed',
    ], default: 0, multiple: true))
    ->reject('none')
    ->map(fn ($option) => match ($option) {
      default => $option,
    })
    ->each(fn ($option) => $input->setOption($option, true));
  }
}
