<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:request')]
class RequestMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:request';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new form request class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Request';

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   */
  public function handle()
  {
    if ($this->validate()) {
      return parent::handle();
    }
  }

  protected function buildClass($name)
  {
    $replace = $this->buildReplacements($name);

    return str_replace(
      array_keys($replace), array_values($replace), parent::buildClass($name)
    );
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath('/stubs/request.stub');
  }

  protected function validate()
  {
    $validator = Validator::make($this->option(), [
      'model' => 'required',
    ]);

    if (! $validator->passes()) {
      $messages = $validator->messages()->toArray();

      foreach ($messages as $message) {
        $this->error($message['0']);
      }

      return false;
    }

    return true;
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    if ($this->option('api')) {
      return $rootNamespace.'Api\Requests';
    }

    return $rootNamespace.'\Domain\Requests';
  }

  protected function buildReplacements($name)
  {
    $class = Str::lower(Str::replace('Request', '', basename(Str::replace('\\', '/', $name))));

    if ($this->option('model')) {
      $modelString = $this->option('model');
      $modelNamespace = $this->qualifyModel($this->option('model'));
    }

    return [
      '{{ action }}' => $class,
      '{{ model }}' => $modelString ?? '',
      '{{ modelNamespace }}' => $modelNamespace ?? '',
    ];
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['api', 'a', InputOption::VALUE_NONE, 'Create requests for your Api.'],
      ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the request applies to'],
    ];
  }
}
