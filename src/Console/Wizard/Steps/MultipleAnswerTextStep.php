<?php

namespace Serenity\Console\Wizard\Steps;

use Serenity\Console\Wizard\Contracts\Wizard;

class MultipleAnswerTextStep extends BaseMultipleAnswerStep
{
  final public function take(Wizard $wizard)
  {
    $wizard->line($this->text);

    $answers = $this->loop(function () {
      return readline();
    });

    if ($this->shouldRemoveEndKeyword($answers)) {
      array_pop($answers);
    }

    return $answers;
  }
}
