<?php

namespace Serenity\Actions\User\TwoFactor\Authentication;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\TwoFactorConfirmedInterface;
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

    return app(TwoFactorConfirmedInterface::class);
  }
}