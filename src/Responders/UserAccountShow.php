<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\UserAccountShow as ContractsUserAccountShow;

class UserAccountShow extends ViewResponder implements ContractsUserAccountShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render(
      $this->component, $this->data
    );
  }
}
