<?php

namespace Serenity\Actions\User\Password;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\FailedPasswordConfirmation;
use Serenity\Contracts\PasswordConfirmed;
use Serenity\Operations\ConfirmPassword;

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
   * Confirm the user's password.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Contracts\Support\Responsable
   */
  public function __invoke(Request $request)
  {
    $confirmed = app(ConfirmPassword::class)(
      $this->guard, $request->user(), $request->input('password')
    );

    if ($confirmed) {
      $request->session()->put('auth.password_confirmed_at', time());
    }

    return $confirmed
      ? app(PasswordConfirmed::class)
      : app(FailedPasswordConfirmation::class);
  }
}
