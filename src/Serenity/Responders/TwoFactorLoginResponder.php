<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\TwoFactorLoginInterface;
use Serenity\Serenity;

class TwoFactorLoginResponder implements TwoFactorLoginInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return redirect()->intended(Serenity::redirects('login'));
  }
}
