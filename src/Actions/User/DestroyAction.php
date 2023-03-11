<?php

namespace Serenity\Actions\User;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Serenity\Action;
use Serenity\Contracts\DeletesUsers;
use Serenity\Operations\ConfirmPassword;

class DestroyAction extends Action
{
  /**
   * Delete the current user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request, StatefulGuard $guard)
  {
    $confirmed = app(ConfirmPassword::class)(
      $guard, $request->user(), $request->password
    );

    if (! $confirmed) {
      throw ValidationException::withMessages([
        'password' => __('The password is incorrect.'),
      ]);
    }

    app(DeletesUsers::class)->delete($request->user()->fresh());

    $guard->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to(url('/'));
  }
}
