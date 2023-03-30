<?php

namespace Serenity\Actions\Documentation;

use Illuminate\Http\Request;
use Serenity\Contracts\DocumentationIndex;
use Serenity\Foundation\Action;
use Serenity\Services\DocumentationService;

class IndexAction extends Action
{
  /**
   * Create a new action instance.
   *
   * @param  \Serenity\Contracts\DocumentationIndex  $responder
   * @param  \Serenity\Services\DocumentationService  $service
   */
  public function __construct(
      protected DocumentationIndex $responder,
      protected DocumentationService $service
    ) {
  }

  /**
   * Invoke our action, handle domain, respond.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request)
  {
    return $this->responder->make(
      $this->service->handle($request)
    )->replace();
  }
}
