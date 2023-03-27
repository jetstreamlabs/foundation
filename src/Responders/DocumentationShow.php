<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\DocumentationShow as DocumentationShowContract;

class DocumentationShow extends ViewResponder implements DocumentationShowContract
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    return $this->view->render($this->component, $this->data);
  }
}
