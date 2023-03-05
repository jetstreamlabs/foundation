<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\TwoFactorEnabledInterface;
use Serenity\Serenity;

class TwoFactorEnabledResponder implements TwoFactorEnabledInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return back()->with('status', Serenity::TWO_FACTOR_AUTHENTICATION_ENABLED);
  }
}
