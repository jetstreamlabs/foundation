<?php

namespace Serenity\Actions\Login;

use Illuminate\Contracts\Auth\StatefulGuard;
use Serenity\Contracts\Login;
use Serenity\Foundation\Action;
use Serenity\Requests\LoginRequest;
use Serenity\Support\LoginPipeline;

class StoreAction extends Action
{
  public function __construct(protected StatefulGuard $guard)
  {
  }

  /**
   * Attempt to authenticate a new session.
   *
   * @param  \Serenity\Requests\LoginRequest  $request
   * @return mixed
   */
  public function __invoke(LoginRequest $request)
  {
    return LoginPipeline::pipe($request)->then(function ($request) {
      return app(Login::class);
    });
  }
}
