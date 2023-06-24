<?php

namespace Serenity\Console\Wizard\Steps;

use Serenity\Console\Wizard\Command\Wizard;

class OneTimeWizard extends Wizard
{
  private array $multiValueSteps;

  public function __construct(array $steps)
  {
    parent::__construct();

    $this->assertStepsAreValid($steps);

    $this->multiValueSteps = $steps;
  }

  public function getSteps(): array
  {
    return $this->multiValueSteps;
  }

  public function completed()
  {
    throw new \RuntimeException('One time wizard cannot reach the completed method.');
  }
}
