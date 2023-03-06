<?php

namespace Serenity\Responders;

use Serenity\Contracts\VerifyEmailViewInterface;
use Serenity\Serenity;

class VerifyEmailResponder implements VerifyEmailViewInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return redirect()->intended(Serenity::redirects('email-verification').'?verified=1');
  }
}