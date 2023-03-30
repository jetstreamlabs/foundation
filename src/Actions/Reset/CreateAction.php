<?php

namespace Serenity\Actions\Reset;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Contracts\ResetPasswordView;
use Serenity\Foundation\Action;

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
   * @return \Serenity\Contracts\ResetPasswordView
   */
  public function __invoke(Request $request): ResetPasswordView
  {
    return app(ResetPasswordView::class);
  }
}
