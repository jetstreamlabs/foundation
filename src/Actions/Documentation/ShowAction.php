<?php

namespace Serenity\Actions\Documentation;

use Serenity\Action;
use Serenity\Contracts\DocumentationShow;
use Serenity\Services\DocumentationService;

class ShowAction extends Action
{
  /**
   * Create a new action instance.
   *
   * @param  \Serenity\Contracts\DocumentationShow  $responder
   * @param  \Serenity\Services\DocumentationService  $service
   */
  public function __construct(
      protected DocumentationShow $responder,
      protected DocumentationService $service
    ) {
    $this->with('Docs/Show', true);
  }

  /**
   * Invoke our action, handle domain, respond.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Contracts\Support\Responsable
   */
  public function __invoke($version, $page = null)
  {
    $payload = $this->service->show($version, $page);
    $data = $payload->getData();

    app('breadcrumbs')->add($data['title'], 'last');

    return $this->responder->make($payload)->send();
  }
}
