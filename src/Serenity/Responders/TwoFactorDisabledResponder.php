<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\TwoFactorDisabledInterface;
use Serenity\Serenity;

class TwoFactorDisabledResponder implements TwoFactorDisabledInterface
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
      ? new JsonResponse('', 200)
      : back()->with('status', Serenity::TWO_FACTOR_AUTHENTICATION_DISABLED);
  }
}
