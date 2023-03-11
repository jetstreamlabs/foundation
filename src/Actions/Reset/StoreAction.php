<?php

namespace Serenity\Actions\Reset;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Serenity\Action;
use Serenity\Contracts\FailedPasswordReset;
use Serenity\Contracts\PasswordReset;
use Serenity\Contracts\ResetsUserPasswords;
use Serenity\Operations\CompletePasswordReset;
use Serenity\Serenity;
use Serenity\Support\Broker;

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
   * Reset the user's password.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Contracts\Support\Responsable
   */
  public function __invoke(Request $request): Responsable
  {
    $request->validate([
      'token' => 'required',
      Serenity::email() => 'required|email',
      'password' => 'required',
    ]);

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $status = Broker::find()->reset(
      $request->only(Serenity::email(), 'password', 'password_confirmation', 'token'),
      function ($user) use ($request) {
        app(ResetsUserPasswords::class)->reset($user, $request->all());

        app(CompletePasswordReset::class)($this->guard, $user);
      }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.
    return $status == Password::PASSWORD_RESET
      ? app(PasswordReset::class, ['status' => $status])
      : app(FailedPasswordReset::class, ['status' => $status]);
  }
}
