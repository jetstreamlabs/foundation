<?php

namespace Serenity\Operations;

use Illuminate\Auth\Events\Lockout;
use Serenity\Contracts\LockoutInterface;
use Serenity\LoginRateLimiter;

class EnsureLoginIsNotThrottled
{
  /**
   * The login rate limiter instance.
   *
   * @var \Serenity\LoginRateLimiter
   */
  protected $limiter;

  /**
   * Create a new class instance.
   *
   * @param  \Serenity\LoginRateLimiter  $limiter
   * @return void
   */
  public function __construct(LoginRateLimiter $limiter)
  {
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
    if (! $this->limiter->tooManyAttempts($request)) {
      return $next($request);
    }

    event(new Lockout($request));

    return app(LockoutInterface::class);
  }
}
