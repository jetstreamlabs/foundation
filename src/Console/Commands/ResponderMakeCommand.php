<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:responder')]
class ResponderMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:responder';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new responder class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Responder';

  public function handle()
  {
    $this->callSilent('make:responder-contract', [
      'name' => "{$this->argument('name')}",
    ]);

    parent::handle();
  }

  /**
   * Build the class with the given name.
   *
   * Remove the base controller import if we are already in the base namespace.
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    $directory = class_basename($this->getNamespace($name));

    $replace = $this->buildInterfaceReplacements($directory);

    return str_replace(
      array_keys($replace), array_values($replace), parent::buildClass($name)
    );
  }

  /**
   * Build the replacements for a parent controller.
   *
   * @param  string  $directory
   * @return array
   */
  protected function buildInterfaceReplacements(string $directory): array
  {
    $namespace = $this->replacementNamespace(ResponderInterfaceMakeCommand::class);

    return [
      '{{ interface }}' => $namespace.'\\'.$directory,
    ];
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/responder.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Responders';
  }
}
