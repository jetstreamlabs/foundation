<?php

namespace Serenity\Console\Wizard\Command;

use Illuminate\Console\GeneratorCommand;
use Serenity\Console\Wizard\Concerns\WizardCore;
use Serenity\Console\Wizard\Contracts\Step;
use Serenity\Console\Wizard\Contracts\Wizard as WizardContract;
use Serenity\Console\Wizard\Exception\AbortWizardException;

abstract class GeneratorWizard extends GeneratorCommand implements Step, WizardContract
{
  use WizardCore { initializeSteps as parentInitializeSteps; }

  const NAME_STEP_NAME = 'name_';

  public function handle()
  {
    try {
      $this->handleWizard();
    } catch (AbortWizardException $e) {
      return $this->abortWizard($e);
    }

    return parent::handle();
  }

  protected function initializeSteps()
  {
    $this->parentInitializeSteps();

    $this->steps->prepend($this->getNameStep(), self::NAME_STEP_NAME);
  }

  abstract protected function getNameStep(): Step;

  abstract protected function generateTarget(): string;

  final protected function getNameInput()
  {
    return $this->answers->get(self::NAME_STEP_NAME);
  }

  final protected function getClassFullName(): string
  {
    return $this->qualifyClass($this->getNameInput());
  }

  final protected function getClassShortName(): string
  {
    $name = $this->getNameInput();
    $class = str_replace($this->getNamespace($name).'\\', '', $name);

    return $class;
  }

  final protected function getClassNamespace(): string
  {
    return $this->getNamespace($this->getClassFullName());
  }

  final protected function buildClass($name)
  {
    return $this->generateTarget();
  }

  final protected function getStub()
  {
  }
}
