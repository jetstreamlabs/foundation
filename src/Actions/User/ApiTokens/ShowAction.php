<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\UserApiTokensShow;
use Serenity\Services\ApiTokenService;

class ShowAction extends Action
{
  public function __construct(
      protected UserApiTokensShow $responder,
      protected ApiTokenService $service
  ) {
    $this->with('API/Index', true);

    bcs(__('Manage API Tokens'), 'last');
  }

  /**
   * Show the user API token screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    return $this->responder->make(
      $this->service->handle($request)
    )->send();
  }
}
