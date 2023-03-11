<?php

namespace Serenity\Actions\TwoFactor\Challenge;

use Illuminate\Contracts\Auth\StatefulGuard;
use Serenity\Action;
use Serenity\Contracts\FailedTwoFactorLogin;
use Serenity\Contracts\TwoFactorLogin;
use Serenity\Events\RecoveryCodeReplaced;
use Serenity\Requests\TwoFactorLoginRequest;

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
   * Attempt to authenticate a new session using the two factor authentication code.
   *
   * @param  \Serenity\Requests\TwoFactorLoginRequest  $request
   * @return mixed
   */
  public function __invoke(TwoFactorLoginRequest $request)
  {
    $user = $request->challengedUser();

    if ($code = $request->validRecoveryCode()) {
      $user->replaceRecoveryCode($code);

      event(new RecoveryCodeReplaced($user, $code));
    } elseif (! $request->hasValidCode()) {
      return app(FailedTwoFactorLogin::class)->toResponse($request);
    }

    $this->guard->login($user, $request->remember());

    $request->session()->regenerate();

    return app(TwoFactorLogin::class);
  }
}
