<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:scope')]
class ScopeMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:scope';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new scope class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Scope';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/scope.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return is_dir(app_path('Domain/Models')) ? $rootNamespace.'\\Domain\\Models\\Scopes' : $rootNamespace.'\\Models\\Scopes';
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the scope already exists'],
    ];
  }
}
