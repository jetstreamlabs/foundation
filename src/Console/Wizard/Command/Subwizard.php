<?php

namespace Serenity\Console\Wizard\Command;

use Serenity\Console\Wizard\Exception\SubwizardException;

abstract class Subwizard extends Wizard
{
  final public function completed()
  {
    throw SubwizardException::completedMethodShouldNotBeCalled();
  }
}
