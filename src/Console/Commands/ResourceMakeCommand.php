<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:resource')]
class ResourceMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:resource';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new resource';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Resource';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    if ($this->collection()) {
      $this->type = 'Resource collection';
    }

    parent::handle();
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->collection()
      ? $this->resolveStubPath('/stubs/resource-collection.stub')
      : $this->resolveStubPath('/stubs/resource.stub');
  }

  /**
   * Determine if the command is generating a resource collection.
   *
   * @return bool
   */
  protected function collection()
  {
    return $this->option('collection') ||
      str_ends_with($this->argument('name'), 'Collection');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Resources';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the resource already exists'],
      ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
    ];
  }
}
