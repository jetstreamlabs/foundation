<?php

namespace Serenity\Actions\TwoFactor\Challenge;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Serenity\Action;
use Serenity\Contracts\TwoFactorChallengeView;
use Serenity\Requests\TwoFactorLoginRequest;

class CreateAction extends Action
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
   * Show the two factor authentication challenge view.
   *
   * @param  \Serenity\Requests\TwoFactorLoginRequest  $request
   * @return \Serenity\Contracts\TwoFactorChallengeView
   */
  public function __invoke(TwoFactorLoginRequest $request): TwoFactorChallengeView
  {
    if (! $request->hasChallengedUser()) {
      throw new HttpResponseException(redirect()->route('login'));
    }

    return app(TwoFactorChallengeView::class);
  }
}
