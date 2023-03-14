<?php

namespace Serenity\Actions\User\Account;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\UserAccountShow;
use Serenity\Services\AccountService;

class ShowAction extends Action
{
  public function __construct(
      protected UserAccountShow $responder,
      protected AccountService $service
    ) {
    $this->with('Settings/Show', true);

    bcs(__('Account Settings'), 'last');
  }

  public function __invoke(Request $request)
  {
    return $this->responder->make(
      $this->service->handle($request)
    )->send();
  }
}
