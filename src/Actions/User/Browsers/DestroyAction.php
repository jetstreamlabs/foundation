<?php

namespace Serenity\Actions\User\Browsers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Serenity\Foundation\Action;
use Serenity\Operations\ConfirmPassword;
use Serenity\Support\Sessions;

class DestroyAction extends Action
{
  /**
   * Log out from other browser sessions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @return \Illuminate\Http\RedirectResponse
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

    $guard->logoutOtherDevices($request->password);

    Sessions::deleteOtherSessionRecords($request);

    return back(303);
  }
}
