<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:channel')]
class ChannelMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:channel';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new channel class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Channel';

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    return str_replace(
      ['DummyUser', '{{ userModel }}'],
      class_basename($this->userProviderModel()),
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
    return $this->resolveStubPath('/stubs/channel.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Broadcasting';
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the channel already exists'],
    ];
  }
}
