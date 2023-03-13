<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\TeamsShow as ContractsTeamsShow;

class TeamsShow extends ViewResponder implements ContractsTeamsShow
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render(
      $this->component, $this->data
    );
  }
}
