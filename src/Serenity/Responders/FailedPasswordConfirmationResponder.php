<?php

namespace Serenity\Responders;

use Illuminate\Validation\ValidationException;
use Serenity\Contracts\FailedPasswordConfirmationInterface;

class FailedPasswordConfirmationResponder implements FailedPasswordConfirmationInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    $message = __('The provided password was incorrect.');

    throw ValidationException::withMessages([
      'password' => [$message],
    ]);
  }
}
