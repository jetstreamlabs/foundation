<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
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
    $domain = $this->rootNamespace().'Domain\\Contracts';

    $class = $domain
      .'\\'
      .Str::replaceFirst($this->rootNamespace(), '', $this->getNamespace($name));

    $replace = $this->buildInterfaceReplacements($class);

    return str_replace(
      array_keys($replace), array_values($replace), parent::buildClass($name)
    );
  }

  /**
   * Build the replacements for a parent controller.
   *
   * @return array
   */
  protected function buildInterfaceReplacements($parentActionClass)
  {
    return [
      'InterfaceClass' => $parentActionClass,
    ];
  }

  /**
   * Build the model replacement values.
   *
   * @param  array  $replace
   * @param  string  $modelClass
   * @return array
   */
  protected function buildFormRequestReplacements(array $replace, $modelClass)
  {
    [$namespace, $storeRequestClass, $updateRequestClass] = [
      'Illuminate\\Http', 'Request', 'Request',
    ];

    if ($this->option('requests')) {
      $namespace = 'App\\Domain\\Requests';

      [$storeRequestClass, $updateRequestClass] = $this->generateFormRequests(
        $modelClass, $storeRequestClass, $updateRequestClass
      );
    }

    $namespacedRequests = $namespace.'\\'.$storeRequestClass.';';

    if ($storeRequestClass !== $updateRequestClass) {
      $namespacedRequests .= PHP_EOL.'use '.$namespace.'\\'.$updateRequestClass.';';
    }

    return array_merge($replace, [
      '{{ storeRequest }}' => $storeRequestClass,
      '{{storeRequest}}' => $storeRequestClass,
      '{{ updateRequest }}' => $updateRequestClass,
      '{{updateRequest}}' => $updateRequestClass,
      '{{ namespacedStoreRequest }}' => $namespace.'\\'.$storeRequestClass,
      '{{namespacedStoreRequest}}' => $namespace.'\\'.$storeRequestClass,
      '{{ namespacedUpdateRequest }}' => $namespace.'\\'.$updateRequestClass,
      '{{namespacedUpdateRequest}}' => $namespace.'\\'.$updateRequestClass,
      '{{ namespacedRequests }}' => $namespacedRequests,
      '{{namespacedRequests}}' => $namespacedRequests,
    ]);
  }

  /**
   * Generate the form requests for the given model and classes.
   *
   * @param  string  $modelClass
   * @param  string  $storeRequestClass
   * @param  string  $updateRequestClass
   * @return array
   */
  protected function generateFormRequests($modelClass, $storeRequestClass, $updateRequestClass)
  {
    $storeRequestClass = 'Store'.class_basename($modelClass).'Request';

    $this->call('make:request', [
      'name' => $storeRequestClass,
    ]);

    $updateRequestClass = 'Update'.class_basename($modelClass).'Request';

    $this->call('make:request', [
      'name' => $updateRequestClass,
    ]);

    return [$storeRequestClass, $updateRequestClass];
  }
}
