<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\UserApiTokensShow as ContractsUserApiTokensShow;

class UserApiTokensShow extends ViewResponder implements ContractsUserApiTokensShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render(
      $this->component, $this->data
    );
  }
}
