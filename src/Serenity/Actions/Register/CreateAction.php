<?php

namespace Serenity\Actions\Register;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\RegisterViewInterface;

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
   * Show the registration view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\RegisterViewInterface
   */
  public function __invoke(Request $request): RegisterViewInterface
  {
    return app(RegisterViewInterface::class);
  }
}
