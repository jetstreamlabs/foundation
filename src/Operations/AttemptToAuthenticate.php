<?php

namespace Serenity\Operations;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;
use Serenity\LoginRateLimiter;
use Serenity\Serenity;

class AttemptToAuthenticate
{
  /**
   * The guard implementation.
   *
   * @var \Illuminate\Contracts\Auth\StatefulGuard
   */
  protected $guard;

  /**
   * The login rate limiter instance.
   *
   * @var \Serenity\LoginRateLimiter
   */
  protected $limiter;

  /**
   * Create a new controller instance.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @param  \Serenity\LoginRateLimiter  $limiter
   * @return void
   */
  public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
  {
    $this->guard = $guard;
    $this->limiter = $limiter;
  }

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  callable  $next
   * @return mixed
   */
  public function handle($request, $next)
  {
    if (Serenity::$authenticateUsingCallback) {
      return $this->handleUsingCustomCallback($request, $next);
    }

    if ($this->guard->attempt(
      $request->only(Serenity::username(), 'password'),
      $request->boolean('remember'))
    ) {
      return $next($request);
    }

    $this->throwFailedAuthenticationException($request);
  }

  /**
   * Attempt to authenticate using a custom callback.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  callable  $next
   * @return mixed
   */
  protected function handleUsingCustomCallback($request, $next)
  {
    $user = call_user_func(Serenity::$authenticateUsingCallback, $request);

    if (! $user) {
      $this->fireFailedEvent($request);

      return $this->throwFailedAuthenticationException($request);
    }

    $this->guard->login($user, $request->boolean('remember'));

    return $next($request);
  }

  /**
   * Throw a failed authentication validation exception.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function throwFailedAuthenticationException($request)
  {
    $this->limiter->increment($request);

    throw ValidationException::withMessages([
      Serenity::username() => [trans('auth.failed')],
    ]);
  }

  /**
   * Fire the failed authentication attempt event with the given arguments.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   */
  protected function fireFailedEvent($request)
  {
    event(new Failed(config('Authenticate.guard'), null, [
      Serenity::username() => $request->{Serenity::username()},
      'password' => $request->password,
    ]));
  }
}
