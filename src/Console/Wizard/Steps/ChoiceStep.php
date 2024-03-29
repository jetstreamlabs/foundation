<?php

namespace Serenity\Console\Wizard\Steps;

use Serenity\Console\Wizard\Contracts\Wizard;

class ChoiceStep extends BaseStep
{
  private array $options;

  public function __construct(string $text, array $options)
  {
    parent::__construct($text);

    $this->options = $options;
  }

  final public function take(Wizard $wizard)
  {
    return $wizard->choice($this->text, $this->options);
  }
}
