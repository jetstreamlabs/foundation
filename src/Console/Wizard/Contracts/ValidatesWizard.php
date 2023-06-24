<?php

namespace Serenity\Console\Wizard\Contracts;

interface ValidatesWizard
{
  public function getRules(): array;

  public function onWizardInvalid(array $errors);
}
