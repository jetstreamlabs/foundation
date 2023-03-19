<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:service')]
class ServiceMakeCommand extends GeneratorCommand
{
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:service';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new service class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Service';

  public function handle()
  {
    if ($this->validate()) {
      return parent::handle();
    }
  }

  protected function validate()
  {
    $validator = Validator::make($this->option(),
      [
        'model' => 'required',
      ], [
        'model.required' => 'The model option is required',
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
   * Build the class with the given name.
   *
   * Remove the base controller import if we are already in the base namespace.
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    if ($this->option('api')) {
      return parent::buildClass($name);
    }

    $replace = $this->buildRequestReplacements($name);

    $this->buildRequests($name);

    return str_replace(
      array_keys($replace), array_values($replace), parent::buildClass($name)
    );
  }

  protected function buildRequestReplacements($name)
  {
    $base = Str::replace('Service', '', class_basename($name));

    $directory = Str::plural($base);

    $main = 'App\\Domain\\Requests\\'.$directory;
    $mainDoc = '\\App\\Domain\\Requests\\'.$directory;

    $files = ['Store', 'Update', 'Delete', 'Restore', 'Destroy'];

    $replacements = [];

    foreach ($files as $file) {
      $replacements['Dummy'.$file.'Request'] = $main.'\\'.$file.'Request';
      $replacements['DocBlock'.$file] = $mainDoc.'\\'.$file.'Request';
      $replacements['{{ dummy'.$file.' }}'] = $file.'Request';
    }

    return $replacements;
  }

  protected function buildRequests(string $name): void
  {
    if ($this->option('model')) {
      $model = $this->option('model');
    } else {
      $model = Str::replace('Service', '', class_basename($name));
    }

    $directory = Str::plural($model);

    $files = ['Store', 'Update', 'Delete', 'Restore', 'Destroy'];

    foreach ($files as $file) {
      $request = $directory.DIRECTORY_SEPARATOR.$file;
      $this->callSilent('make:request', [
        'name' => "{$request}Request",
        '--model' => $model,
      ]);
    }
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    if ($this->option('api')) {
      $stub = '/stubs/service.api.stub';
    } else {
      $stub = '/stubs/service.stub';
    }

    return $this->resolveStubPath($stub);
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
      return $rootNamespace.'\\Api\\Services';
    }

    return $rootNamespace.'\\Domain\\Services';
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['api', 'a', InputOption::VALUE_NONE, 'Create a service for your Api'],
      ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the request applies to'],
    ];
  }
}
