<?php

namespace Serenity\Console\Wizard\Exception;

use Throwable;

class AbortWizardException extends \Exception
{
  protected $message = 'Wizard abort initiated by client.';

  private ?string $userMessage = null;

  public function __construct(string $userMessage = null, $code = 0, Throwable $previous = null)
  {
    parent::__construct($userMessage, $code, $previous);

    $this->userMessage = $userMessage;
  }

  public function getUserMessage()
  {
    return $this->userMessage;
  }
}
