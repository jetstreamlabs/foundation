<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:repository-contract')]
class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $name = 'make:repository-contract';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new repository interface.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Repository Interface';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/repository-interface.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Contracts\Repositories';
  }
}
