<?php

namespace Serenity\Actions\User\TwoFactor\Authentication;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\TwoFactorEnabled;
use Serenity\Operations\EnableTwoFactorAuthentication;

class StoreAction extends Action
{
  /**
   * Enable two factor authentication for the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Operations\EnableTwoFactorAuthentication  $enable
   * @return \Serenity\Contracts\TwoFactorEnabled
   */
  public function __invoke(Request $request, EnableTwoFactorAuthentication $enable)
  {
    $enable($request->user());

    return app(TwoFactorEnabled::class);
  }
}
