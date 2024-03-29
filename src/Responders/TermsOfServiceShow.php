<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\TermsOfServiceShow as ContractsTermsOfServiceShow;

class TermsOfServiceShow extends ViewResponder implements ContractsTermsOfServiceShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component);
  }
}
