<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:page')]
class PageMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:page';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new inertia page view';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Page';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/page.stub');
  }

  /**
   * Get the destination class path.
   *
   * @param  string  $name
   * @return string
   */
  protected function getPath($name)
  {
    $name = '\\'.$name;

    return resource_path('js').str_replace('\\', '/', $name).'.vue';
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace;
  }

  /**
   * Get the root namespace for the class.
   *
   * @return string
   */
  protected function rootNamespace()
  {
    return 'Pages';
  }
}
