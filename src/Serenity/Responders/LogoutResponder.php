<?php

namespace Serenity\Responders;

use Serenity\Contracts\LogoutInterface;
use Serenity\Serenity;

class LogoutResponder implements LogoutInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return redirect(Serenity::redirects('logout', '/'));
  }
}
