<?php

namespace Serenity\Responders;

use Serenity\Contracts\LoginInterface;
use Serenity\Serenity;

class LoginResponder implements LoginInterface
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
