<?php

namespace Serenity\Actions\Logout;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\Logout;

class DestroyAction extends Action
{
  /**
   * Create a new controller instance.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @return void
   */
  public function __construct(protected StatefulGuard $guard)
  {
  }

  /**
   * Destroy an authenticated session.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\Logout
   */
  public function __invoke(Request $request): Logout
  {
    $this->guard->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return app(Logout::class);
  }
}
