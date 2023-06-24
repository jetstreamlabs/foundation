<?php

namespace Serenity\Console\Wizard\Steps;

use Serenity\Console\Wizard\Contracts\Step;

abstract class BaseStep implements Step
{
  protected string $text;

  public function __construct(string $text)
  {
    $this->text = $text;
  }
}
