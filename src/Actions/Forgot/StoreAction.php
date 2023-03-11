<?php

namespace Serenity\Actions\Forgot;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Serenity\Action;
use Serenity\Contracts\FailedPasswordResetLinkRequest;
use Serenity\Contracts\SuccessfulPasswordResetLinkRequest;
use Serenity\Serenity;
use Serenity\Support\Broker;

class StoreAction extends Action
{
  /**
   * Send a reset link to the given user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Contracts\Support\Responsable
   */
  public function __invoke(Request $request): Responsable
  {
    $request->validate([Serenity::email() => 'required|email']);

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Broker::find()->sendResetLink(
      $request->only(Serenity::email())
    );

    return $status == Password::RESET_LINK_SENT
      ? app(SuccessfulPasswordResetLinkRequest::class, ['status' => $status])
      : app(FailedPasswordResetLinkRequest::class, ['status' => $status]);
  }
}
