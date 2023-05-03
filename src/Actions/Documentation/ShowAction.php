<?php

namespace Serenity\Actions\Documentation;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Serenity\Contracts\DocumentationShow;
use Serenity\Foundation\Action;
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
  }

  /**
   * Invoke our action, handle domain, respond.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Contracts\Support\Responsable
   */
  public function __invoke(Request $request, $version, $page = null)
  {
    $template = Str::of($request->getPathInfo())
      ->replaceFirst('/docs', 'Docs')
      ->value;

    $payload = $this->service->show($version, $page);
    $data = $payload->getData();

    if ($payload->getStatus() === 404) {
      $template = 'Docs/404';
    }

    app('breadcrumbs')->add($data['title'], 'last');

    $this->with($template, true);

    return $this->responder->make($payload)->send();
  }
}
