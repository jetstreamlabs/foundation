<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\TeamsCreate as ContractsTeamsCreate;

class TeamsCreate extends ViewResponder implements ContractsTeamsCreate
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component);
  }
}
