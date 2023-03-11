<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\UserProfileShow as ContractsUserProfileShow;
use Serenity\Responders\ViewResponder;

class UserProfileShow extends ViewResponder implements ContractsUserProfileShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component, $this->data);
  }
}
