<?php

namespace Serenity\Responders;

use Illuminate\Support\Facades\Redirect;
use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\DocumentationIndex as DocumentationIndexContract;

class DocumentationIndex extends ViewResponder implements DocumentationIndexContract
{
  use ProvidesResponderMethods;

  public function toResponse(string $route, bool $message = false)
  {
    if ($message) {
      return Redirect::to($route)
        ->with($this->payload->getLevel(), $this->payload->getMessage());
    }

    return Redirect::to($route);
  }
}
