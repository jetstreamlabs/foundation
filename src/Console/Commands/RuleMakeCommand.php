<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:rule')]
class RuleMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:rule';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new validation rule';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Rule';

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
    return str_replace(
      '{{ ruleType }}',
      $this->option('implicit') ? 'ImplicitRule' : 'Rule',
      parent::buildClass($name)
    );
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->option('implicit')
        ? $this->resolveStubPath('/stubs/rule.implicit.stub')
        : $this->resolveStubPath('/stubs/rule.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Rules';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the rule already exists'],
      ['implicit', 'i', InputOption::VALUE_NONE, 'Generate an implicit rule'],
    ];
  }
}
