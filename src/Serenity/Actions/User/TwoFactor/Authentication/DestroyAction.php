<?php

namespace Serenity\Actions\User\TwoFactor\Authentication;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\TwoFactorDisabledInterface;
use Serenity\Operations\DisableTwoFactorAuthentication;

class DestroyAction extends Action
{
  /**
   * Disable two factor authentication for the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Operations\DisableTwoFactorAuthentication  $disable
   * @return \Serenity\Contracts\TwoFactorDisabledInterface
   */
  public function __invoke(Request $request, DisableTwoFactorAuthentication $disable)
  {
    $disable($request->user());

    return app(TwoFactorDisabledInterface::class);
  }
}
