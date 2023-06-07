<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\TwoFactorConfirmed as TwoFactorConfirmedInterface;
use Serenity\Serenity;

class TwoFactorConfirmed implements TwoFactorConfirmedInterface
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
      : back()->with('status', trans('app.'.Serenity::TWO_FACTOR_AUTHENTICATION_CONFIRMED));
  }
}
