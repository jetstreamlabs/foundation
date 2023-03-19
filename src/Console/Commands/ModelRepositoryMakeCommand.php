<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:model-repository')]
class ModelRepositoryMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:model-repository';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create Eloquent repository.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Repository';

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   *
   * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
   */
  protected function buildClass($name)
  {
    $stub = $this->files->get($this->getStub());

    if ($this->option('model')) {
      $stub = $this->replaceEntityName($stub);
    }

    return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
  }

  /**
   * Replace the entity name in the repository.
   *
   * @param  string  $stub
   * @return string
   */
  protected function replaceEntityName($stub)
  {
    $stub = str_replace('DummyEntity', Str::singular($this->option('model')), $stub);

    return $stub;
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/repository.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Repositories';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['model', 'm', InputOption::VALUE_REQUIRED, 'Set the model for the repository.'],
    ];
  }
}
