<?php

namespace Serenity\Actions\Reset;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\ResetPasswordViewInterface;

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
   * Show the new password view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\ResetPasswordViewInterface
   */
  public function __invoke(Request $request): ResetPasswordViewInterface
  {
    return app(ResetPasswordViewInterface::class);
  }
}
