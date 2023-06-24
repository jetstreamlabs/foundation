<?php

namespace Serenity\Console\Wizard\Command;

use Illuminate\Console\Command;
use Serenity\Console\Wizard\Concerns\WizardCore;
use Serenity\Console\Wizard\Contracts\Wizard as WizardContract;
use Serenity\Console\Wizard\Exception\AbortWizardException;

abstract class Wizard extends Command implements WizardContract
{
  use WizardCore;

  public function __construct()
  {
    parent::__construct();
  }

  final public function handle()
  {
    try {
      $this->handleWizard();
    } catch (AbortWizardException $e) {
      return $this->abortWizard($e);
    }

    $this->completed();
  }

  abstract public function getSteps(): array;

  abstract public function completed();
}
