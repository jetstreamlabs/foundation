<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\PrivacyPolicyShow as ContractsPrivacyPolicyShow;

class PrivacyPolicyShow extends ViewResponder implements ContractsPrivacyPolicyShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component);
  }
}
