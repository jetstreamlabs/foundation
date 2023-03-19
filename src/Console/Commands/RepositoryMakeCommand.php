<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:repository')]
class RepositoryMakeCommand extends GeneratorCommand
{
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:repository';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create repository and interface.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Repository';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    // none required, this only calls other generators.
  }

  /**
   * Execute the console command.
   *
   * @return bool|null
   */
  public function handle()
  {
    $this->createInterface();
    $this->createRepository();
  }

  /**
   * Create an interface for the repo.
   *
   * @return void
   */
  protected function createInterface()
  {
    $interface = Str::studly(class_basename($this->argument('name')));

    $this->call('make:repository-contract', [
      'name' => "{$interface}",
    ]);
  }

  /**
   * Create an Eloquent repository concrete.
   *
   * @return void
   */
  protected function createRepository()
  {
    $repository = Str::studly(class_basename($this->argument('name')));

    $this->call('make:model-repository', [
      'name' => "{$repository}",
      '--model' => $this->argument('model') ?? null,
    ]);
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
      ['model', InputArgument::REQUIRED, 'Set the model for the repository.'],
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
      'name' => 'What should the '.strtolower($this->type).' be named?',
      'model' => 'What model is this repository for?',
    ];
  }
}
