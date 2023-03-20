<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:action')]
class ActionMakeCommand extends GeneratorCommand
{
  use CreatesMatchingTest;
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:action';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new action class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Action';

  public function handle()
  {
    if ($this->validateName() && $this->validate()) {
      if ($this->option('api')) {
        return parent::handle();
      }

      if ($this->option('resp')) {
        $responder = Str::replace('Action', 'Responder', $this->argument('name'));

        $this->call('make:responder', [
          'name' => "{$responder}",
        ]);
      }

      return parent::handle();
    }
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    $stub = null;

    if ($this->option('plain')) {
      $stub = '/stubs/action.plain.stub';
    } elseif ($this->option('api')) {
      $stub = '/stubs/action.api.stub';
    } else {
      $stub = '/stubs/action.stub';
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
      return $rootNamespace.'\Api';
    } elseif ($this->argument('visibility')) {
      $visibility = match (mb_strtolower($this->argument('visibility'))) {
        'g' => '\Actions\Public',
        'm' => '\Actions\Protected',
        'a' => '\Actions\Private',
        'n' => '\Actions'
      };

      return $rootNamespace.$visibility;
    } else {
      return $rootNamespace.'\Actions';
    }
  }

  /**
   * Validate our model option to ensure it's been set.
   *
   * @return bool
   */
  protected function validate(): bool
  {
    $validator = Validator::make($this->option(),
      [
        'model' => 'required',
        '',
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
   * Validate our name argument to make sure it ends in Action.
   *
   * @return bool
   */
  protected function validateName(): bool
  {
    $validator = Validator::make($this->argument(),
      [
        'name' => 'ends_with:Action',
      ], [
        'name.ends_with' => "An action must end with the word Action in it's name",
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
      $replace = $this->buildServiceReplacements($this->getNamespace($name));

      return str_replace(
        array_keys($replace), array_values($replace), parent::buildClass($name)
      );
    }

    $replace = $this->buildResponderReplacements($name);

    $replace = $this->buildFormRequestReplacements($replace, $name);

    return str_replace(
      array_keys($replace), array_values($replace), parent::buildClass($name)
    );
  }

  /**
   * Build replacements for Services.
   *
   * @param  string  $name
   * @return array
   */
  protected function buildServiceReplacements(string $name): array
  {
    $service = Str::singular(class_basename($name)).'Service';

    $action = Str::replace('Action', '', class_basename($this->argument('name')));

    return [
      '{{ service }}' => $service,
      '{{ serviceAction }}' => Str::lower($action),
    ];
  }

  /**
   * Build replacements for responders.
   *
   * @param  string  $name
   * @return array
   */
  protected function buildResponderReplacements(string $name): array
  {
    $responder = Str::replace('Action', 'Responder', class_basename($name));

    $directory = class_basename($this->getNamespace($name));

    $namespace = $this->replacementNamespace(ResponderInterfaceMakeCommand::class);

    $namespace = $namespace.'\\'.$directory;

    $page = Str::replace('Action', '', implode('/', array_slice(explode('\\', $name), -2)));

    return [
      '{{ responderContract }}' => $namespace.'\\'.$responder,
      '{{ responder }}' => $responder,
      '{{ inertiaPage }}' => $page,
    ];
  }

  /**
   * Build the request replacement values.
   *
   * @param  array  $replace
   * @param  string  $modelClass
   * @return array
   */
  protected function buildFormRequestReplacements(array $replace, $name)
  {
    $actions = ['Index', 'Show', 'Create', 'Edit'];

    [$namespace, $viewRequestClass] = [
      'Illuminate\\Http', 'Request',
    ];

    $action = Str::replace('Action', '', class_basename($this->argument('name')));

    $actionRequest = $viewRequestClass;

    if (in_array($action, $actions)) {
      $namespace = 'App\\Domain\\Requests';

      $requestClass = class_basename($this->getNamespace($name));

      $viewRequestClass = $this->generateFormRequests($requestClass);

      $actionRequest = class_basename($viewRequestClass);
    }

    $namespacedRequests = $namespace.'\\'.$viewRequestClass.';';

    return array_merge($replace, [
      '{{ viewRequest }}' => $actionRequest,
      '{{ namespacedViewRequest }}' => $namespace.'\\'.$viewRequestClass,
      '{{ namespacedRequests }}' => $namespacedRequests,
    ]);
  }

  /**
   * Generate the form requests for the given model and classes.
   *
   * @param  string  $requestClass
   * @return array
   */
  protected function generateFormRequests($requestClass)
  {
    $viewRequestClass = $requestClass.'\\'.'ViewRequest';

    $this->callSilent('make:request', [
      'name' => $viewRequestClass,
      '--model' => $this->option('model'),
    ]);

    return $viewRequestClass;
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The '.strtolower($this->type).' to create for the app'],
      ['visibility', InputArgument::REQUIRED, 'Set the visibility for the namespace (G: Public, M: Protected, A: Private)'],
    ];
  }

  /**
   * Prompt for missing input arguments using the returned questions.
   *
   * @return array
   */
  protected function promptForMissingArgumentsUsing()
  {
    return [
      'name' => 'The '.strtolower($this->type).' to create for the app?',
      'visibility' => 'Set the visibility for the namespace (G: Public, M: Protected, A: Private N: None/Api)',
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['plain', 'p', InputOption::VALUE_NONE, 'Generate an empty action class'],
      ['api', 'a', InputOption::VALUE_NONE, 'Generate action for your Api namespace'],
      ['resp', 'r', InputOption::VALUE_NONE, 'Generate a responder for UI actions'],
      ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the request applies to'],
    ];
  }
}
