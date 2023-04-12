<?php

namespace Serenity\Responders;

use Serenity\Concerns\ProvidesResponderMethods;
use Serenity\Contracts\DocumentationIndex as DocumentationIndexContract;

class DocumentationIndex extends ViewResponder implements DocumentationIndexContract
{
  use ProvidesResponderMethods;

  public function toResponse()
  {
    $version = $this->data['version'];
    $page = $this->data['page'];

    return redirect()->route('docs.show', ['version' => $version, 'page' => $page]);
  }
}
