<?php

namespace Serenity\Console\Wizard\Contracts;

interface Step
{
  public function take(Wizard $wizard);
}
