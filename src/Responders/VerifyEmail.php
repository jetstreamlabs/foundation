<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\VerifyEmailView;
use Serenity\Serenity;

class VerifyEmail implements VerifyEmailView
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return $request->wantsJson()
      ? new JsonResponse('', 204)
      : redirect()->intended(Serenity::redirects('email-verification').'?verified=1');
  }
}
