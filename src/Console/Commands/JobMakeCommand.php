<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:job')]
class JobMakeCommand extends GeneratorCommand
{
  use CreatesMatchingTest;
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:job';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new job class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Job';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->option('sync')
      ? $this->resolveStubPath('/stubs/job.stub')
      : $this->resolveStubPath('/stubs/job.queued.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Jobs';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the job already exists'],
      ['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous'],
    ];
  }
}
