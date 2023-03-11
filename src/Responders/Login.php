<?php

namespace Serenity\Responders;

use Serenity\Contracts\Login as LoginInterface;
use Serenity\Serenity;

class Login implements LoginInterface
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
      ? response()->json(['two_factor' => false])
      : redirect()->intended(Serenity::redirects('login'));
  }
}
