<?php

namespace Serenity\Responders;

use Inertia\Inertia;
use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\PrivacyPolicyShow as ContractsPrivacyPolicyShow;

class PrivacyPolicyShow extends ViewResponder implements ContractsPrivacyPolicyShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return Inertia::render($this->component);
  }
}
