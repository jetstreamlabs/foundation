<?php

namespace Serenity\Actions\Email\Verify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Serenity;

class StoreAction extends Action
{
  /**
   * Send a new email verification notification.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    if ($request->user()->hasVerifiedEmail()) {
      return $request->wantsJson()
        ? new JsonResponse('', 204)
        : redirect()->intended(Serenity::redirects('email-verification'));
    }

    $request->user()->sendEmailVerificationNotification();

    return $request->wantsJson()
      ? new JsonResponse('', 202)
      : back()->with('status', Serenity::VERIFICATION_LINK_SENT);
  }
}
