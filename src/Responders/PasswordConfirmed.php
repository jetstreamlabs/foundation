<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\PasswordConfirmed as PasswordConfirmedInterface;
use Serenity\Serenity;

class PasswordConfirmed implements PasswordConfirmedInterface
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
      ? new JsonResponse('', 201)
      : redirect()->intended(Serenity::redirects('password-confirmation'));
  }
}
