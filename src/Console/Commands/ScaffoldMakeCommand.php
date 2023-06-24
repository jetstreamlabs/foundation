<?php

namespace Serenity\Console\Commands;

use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Serenity\Console\Wizard\Command\AggregateWizard;
use Serenity\Console\Wizard\Contracts\Step;
use Serenity\Console\Wizard\Steps\ChoiceStep;
use Serenity\Console\Wizard\Steps\ConfirmStep;
use Serenity\Console\Wizard\Steps\TextStep;
use Serenity\Console\Wizard\Steps\UniqueMultipleChoiceStep;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:scaffold')]
class ScaffoldMakeCommand extends AggregateWizard
{
  use ResolvesStubPath;

  protected $signature = 'make:scaffold';

  protected $description = 'Namespace scaffold wizard.';

  /**
   * Generate the steps for the wizard.
   */
  public function getSteps(): array
  {
    return [
      'visibility' => new ChoiceStep('What visibility should the namespace have?', [
        'Public', 'Protected', 'Private', 'API',
      ]),
      'actions' => new UniqueMultipleChoiceStep('Select all the actions to create for your namespace', [
        'Index', 'Create', 'Show', 'Store', 'Edit', 'Update', 'Delete', 'Restore', 'Destroy',
      ], [
        'end_keyword' => 'done',
        'retain_end_keyword' => false,
      ]),
      'model' => new TextStep('Enter the name of the Model to use for this namespace'),
      'service' => new ConfirmStep('Do you want to create a service?', true),
      'repository' => new ConfirmStep('Would you like a repository?', true),
      'observer' => new ConfirmStep('Do you need an Observer?'),
    ];
  }

  /**
   * First wizard step - ie: name.
   */
  public function getNameStep(): Step
  {
    return new TextStep('What namespace would you like to create?');
  }

  /**
   * Message for pre-action step.
   */
  public function takingActions(Step $step)
  {
    $this->info("Next we'll select your actions. Each action you create will automatically create");
    $this->info('a responder and a corresponding Vue page. There will also be a single namespace service');
    $this->info('with corresponding Form Requests for each action, and a Policy to govern action access.');
  }

  /**
   * Run all generators, mocks `handle`
   */
  public function generateTarget(): string
  {
    $this->createModel();
    $this->createService();

    $this->answers->get('visibility') === 'API'
      ? $this->createApiActions()
      : $this->createAppActions();

    $this->createRepository();
    $this->createObserver();

    return '';
  }

  /**
   * Generate a repository if requested.
   */
  protected function createRepository(): void
  {
    if ($this->answers->get('repository')) {
      $name = Str::singular($this->answers->get('name_'));

      $model = Str::studly(Str::singular($this->answers->get('model')));

      $this->call('make:repository', [
        'name' => "{$name}Repository",
        'model' => $model,
      ]);
    }
  }

  /**
   * Generate an observer if requested.
   */
  protected function createObserver(): void
  {
    if ($this->answers->get('observer')) {
      $name = Str::singular($this->answers->get('name_'));

      $model = Str::studly(Str::singular($this->answers->get('model')));

      $this->call('make:observer', [
        'name' => "{$name}Observer",
        '--model' => $model,
      ]);
    }
  }

  /**
   * Generate a service if requested.
   */
  protected function createService(): void
  {
    if ($this->answers->get('service')) {
      $service = Str::singular($this->answers->get('name_'));

      $model = Str::studly(Str::singular($this->answers->get('model')));

      $this->call('make:service', [
        'name' => "{$service}Service",
        '--model' => $model,
        '--api' => $this->answers->get('visibility') === 'API' ?? false,
      ]);
    }
  }

  /**
   * Generate the model.
   */
  protected function createModel(): void
  {
    $name = $this->qualifyModel($this->answers->get('model'));

    if ($this->alreadyExists($name)) {
      $this->info('This model already exists, skipping');
    } else {
      $this->call('make:model', [
        'name' => Str::studly(Str::singular($this->answers->get('model'))),
        '--migration' => true,
        '--factory' => true,
        '--policy' => true,
      ]);
    }
  }

  /**
   * Generate a Vue page for each action requested.
   */
  protected function createPage(string $file): void
  {
    $files = ['Index', 'Create', 'Show', 'Edit'];

    $namespace = Str::studly(Str::replace('\\', '/', $this->answers->get('name_')));

    if (in_array($file, $files)) {
      $page = $namespace.DIRECTORY_SEPARATOR.$file;

      $this->call('make:page', [
        'name' => "{$page}",
      ]);
    }
  }

  /**
   * Generate actions for the main app.
   */
  protected function createAppActions(): void
  {
    $namespace = Str::studly(Str::replace('\\', '/', $this->answers->get('name_')));

    $model = Str::studly(Str::singular($this->answers->get('model')));

    $files = $this->answers->get('actions');

    foreach ($files as $file) {
      $action = $namespace
        .DIRECTORY_SEPARATOR
        .$file;

      $this->call('make:action', [
        'name' => "{$action}Action",
        'visibility' => $this->convertVisibility($this->answers->get('visibility')),
        '--model' => $model,
        '--resp' => true,
      ]);

      $this->createPage($file);
    }
  }

  /**
   * Generate API actions if requested.
   */
  protected function createApiActions(): void
  {
    $namespace = Str::studly(Str::replace('\\', '/', $this->answers->get('name_')));

    $model = Str::studly(Str::singular($this->answers->get('model')));

    $files = $this->answers->get('actions');

    foreach ($files as $file) {
      $action = $namespace
        .DIRECTORY_SEPARATOR
        .$file;

      $this->call('make:action', [
        'name' => "{$action}Action",
        'visibility' => 'N',
        '--model' => $model,
        '--api' => true,
      ]);
    }
  }

  /**
   * Convert the requested plain text visibility to var
   * for passing into given generators.
   */
  protected function convertVisibility(string $choice): string
  {
    return match ($choice) {
      'Public' => 'G',
      'Protected' => 'M',
      'Private' => 'A',
      'API' => 'N'
    };
  }
}
