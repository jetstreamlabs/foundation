<?php

namespace Serenity\Responders;

use Serenity\Contracts\RegisterInterface;
use Serenity\Serenity;

class RegisterResponder implements RegisterInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return redirect()->intended(Serenity::redirects('register'));
  }
}
