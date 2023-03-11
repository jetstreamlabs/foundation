<?php

namespace Serenity\Responders;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Serenity\Contracts\LockoutInterface;
use Serenity\LoginRateLimiter;
use Serenity\Serenity;

class LockoutResponder implements LockoutInterface
{
  /**
   * Create a new response instance.
   *
   * @param  \Serenity\LoginRateLimiter  $limiter
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
