<?php

namespace Serenity\Actions\Login;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\LoginViewInterface;

class CreateAction extends Action
{
  /**
   * Create a new action instance.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard $guard
   */
  public function __construct(protected StatefulGuard $guard)
  {
  }

  /**
   * Show the login view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\LoginViewInterface
   */
  public function __invoke(Request $request): LoginViewInterface
  {
    return app(LoginViewInterface::class);
  }
}
