<?php

namespace Serenity\Console\Wizard\Command\Generators\Subwizards;

use Serenity\Console\Wizard\Command\Subwizard;
use Serenity\Console\Wizard\Steps\TextStep;

class MultipleChoiceOptionsSubwizard extends Subwizard
{
  public function getSteps(): array
  {
    return [
      'options' => $this->repeat(new TextStep("Add option for multiple choice (enter 'stop' to stop)"))->untilAnswerIs('stop')->withoutLastAnswer(),
    ];
  }
}
