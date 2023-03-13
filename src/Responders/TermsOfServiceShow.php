<?php

namespace Serenity\Responders;

use Inertia\Inertia;
use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\TermsOfServiceShow as ContractsTermsOfServiceShow;
use Serenity\Responders\ViewResponder;

class TermsOfServiceShow extends ViewResponder implements ContractsTermsOfServiceShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return Inertia::render($this->component);
  }
}
