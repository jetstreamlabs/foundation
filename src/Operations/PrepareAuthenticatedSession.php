<?php

namespace Serenity\Operations;

use Serenity\Routing\LoginRateLimiter;

class PrepareAuthenticatedSession
{
  /**
   * The login rate limiter instance.
   *
   * @var Serenity\Routing\LoginRateLimiter
   */
  protected $limiter;

  /**
   * Create a new class instance.
   *
   * @param  Serenity\Routing\LoginRateLimiter  $limiter
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
    if ($request->hasSession()) {
      $request->session()->regenerate();
    }

    $this->limiter->clear($request);

    return $next($request);
  }
}
