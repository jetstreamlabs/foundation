<?php

namespace Serenity\Actions\User\TwoFactor\Authentication;

use Illuminate\Http\Request;
use Serenity\Contracts\TwoFactorConfirmed;
use Serenity\Foundation\Action;
use Serenity\Operations\ConfirmTwoFactorAuthentication;

class UpdateAction extends Action
{
  /**
   * Enable two factor authentication for the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Operations\ConfirmTwoFactorAuthentication  $confirm
   * @return \Serenity\Contracts\TwoFactorConfirmedInterface
   */
  public function __invoke(Request $request, ConfirmTwoFactorAuthentication $confirm)
  {
    $confirm($request->user(), $request->input('code'));

    return app(TwoFactorConfirmed::class);
  }
}
