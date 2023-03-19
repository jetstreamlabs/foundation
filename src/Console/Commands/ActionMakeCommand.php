<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
    if ($this->validate()) {
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

    if ($this->option('basic')) {
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
    }

    return $rootNamespace.'\Actions';
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
   * @param  string  $actionClass
   * @return array
   */
  protected function buildServiceReplacements(string $actionClass): array
  {
    $service = Str::singular(class_basename($actionClass)).'Service';

    $action = Str::replace('Action', '', class_basename($this->argument('name')));

    return [
      'DummyService' => $service,
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

    $classes = [
      '\\App\\Domain\\Contracts\\Responders\\'.class_basename($this->getNamespace($name)),
      $responder,
    ];

    $responderContract = implode('\\', $classes);

    $page = Str::replace('Action', '', implode('/', array_slice(explode('\\', $name), -2)));

    return [
      'DummyResponderContract' => $responderContract,
      'DummyResponder' => $responder,
      '{{ dummyPage }}' => $page,
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
  protected function getOptions()
  {
    return [
      ['standard', null, InputOption::VALUE_NONE, 'Generate a standard actions for the given namespace'],
      ['api', 'a', InputOption::VALUE_NONE, 'Exclude the create and edit action from the namespace'],
      ['basic', 'b', InputOption::VALUE_OPTIONAL, 'Generate a basic actions for the given namespace'],
      ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the request applies to'],
    ];
  }

  /**
   * Interact further with the user if they were prompted for missing arguments.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
  protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
  {
    if ($this->didReceiveOptions($input)) {
      return;
    }

    $type = $this->components->choice('Which type of action would you like?', [
      'standard',
      'basic',
      'api',
    ], default: 0);

    $input->setOption($type, true);
  }
}
