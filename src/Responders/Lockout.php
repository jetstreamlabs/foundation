<?php

namespace Serenity\Responders;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Serenity\Contracts\Lockout as LockoutInterface;
use Serenity\Routing\LoginRateLimiter;
use Serenity\Serenity;

class Lockout implements LockoutInterface
{
  /**
   * Create a new response instance.
   *
   * @param  \Serenity\Routing\LoginRateLimiter  $limiter
   * @return void
   */
  public function __construct(
      protected LoginRateLimiter $limiter
    ) {
  }

  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return with($this->limiter->availableIn($request), function ($seconds) {
      throw ValidationException::withMessages([
        Serenity::username() => [
          trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
          ]),
        ],
      ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    });
  }
}
