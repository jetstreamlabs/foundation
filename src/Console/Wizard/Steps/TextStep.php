<?php

namespace Serenity\Console\Wizard\Steps;

use Serenity\Console\Wizard\Contracts\Wizard;

class TextStep extends BaseStep
{
  final public function take(Wizard $wizard)
  {
    return $wizard->ask($this->text);
  }
}
