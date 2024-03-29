<?php

namespace Serenity\Console\Wizard\Exception;

class SubwizardException extends \Exception
{
  public static function completedMethodShouldNotBeCalled(): self
  {
    return new self('Subwizard::completed() method should not be called.');
  }
}
