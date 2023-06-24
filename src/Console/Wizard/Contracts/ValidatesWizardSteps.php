<?php

namespace Serenity\Console\Wizard\Contracts;

interface ValidatesWizardSteps
{
  public function getRules(): array;
}
