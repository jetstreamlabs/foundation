<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\PasswordUpdateInterface;
use Serenity\Serenity;

class PasswordUpdateResponder implements PasswordUpdateInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return back()->with('status', Serenity::PASSWORD_UPDATED);
  }
}
