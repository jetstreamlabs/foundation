<?php

namespace Serenity\Responders;

use Serenity\Contracts\TwoFactorConfirmedInterface;
use Serenity\Serenity;

class TwoFactorConfirmedResponder implements TwoFactorConfirmedInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return back()->with('status', Serenity::TWO_FACTOR_AUTHENTICATION_CONFIRMED);
  }
}
