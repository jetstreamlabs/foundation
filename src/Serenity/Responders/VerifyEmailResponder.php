<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\VerifyEmailViewInterface;
use Serenity\Serenity;

class VerifyEmailResponder implements VerifyEmailViewInterface
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
