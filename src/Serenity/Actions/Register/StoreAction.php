<?php

namespace Serenity\Actions\Register;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\CreatesNewUsersInterface;
use Serenity\Contracts\RegisterInterface;

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
   * @param  \Serenity\Contracts\CreatesNewUsersInterface  $creator
   * @return \Serenity\Contracts\RegisterInterface
   */
  public function __invoke(Request $request, CreatesNewUsersInterface $creator): RegisterInterface
  {
    event(new Registered($user = $creator->create($request->all())));

    $this->guard->login($user);

    return app(RegisterInterface::class);
  }
}
