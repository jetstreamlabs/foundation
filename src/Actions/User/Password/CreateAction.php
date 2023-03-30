<?php

namespace Serenity\Actions\User\Password;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Contracts\ConfirmPasswordView;
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
   * Show the confirm password view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\ConfirmPasswordView
   */
  public function __invoke(Request $request): ConfirmPasswordView
  {
    return app(ConfirmPasswordView::class);
  }
}
