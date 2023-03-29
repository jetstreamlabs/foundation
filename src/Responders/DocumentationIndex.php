<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\DocumentationIndex as DocumentationIndexContract;

class DocumentationIndex extends ViewResponder implements DocumentationIndexContract
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->location($this->route);
  }
}
