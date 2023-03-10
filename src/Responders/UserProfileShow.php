<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\UserProfileShow as ContractsUserProfileShow;

class UserProfileShow extends ViewResponder implements ContractsUserProfileShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component, $this->data);
  }
}
