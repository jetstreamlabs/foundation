<?php

namespace Serenity\Actions\Register;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\CreatesNewUsers;
use Serenity\Contracts\Register;

class StoreAction extends Action
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
   * Create a new registered user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Contracts\CreatesNewUsers  $creator
   * @return \Serenity\Contracts\Register
   */
  public function __invoke(Request $request, CreatesNewUsers $creator): Register
  {
    event(new Registered($user = $creator->create($request->all())));

    $this->guard->login($user);

    return app(Register::class);
  }
}
