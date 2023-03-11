<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\TwoFactorLogin as TwoFactorLoginInterface;
use Serenity\Serenity;

class TwoFactorLogin implements TwoFactorLoginInterface
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
      : redirect()->intended(Serenity::redirects('login'));
  }
}
