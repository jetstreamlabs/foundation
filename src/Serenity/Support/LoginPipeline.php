<?php

namespace Serenity\Support;

use Illuminate\Routing\Pipeline;
use Serenity\Features;
use Serenity\Operations\AttemptToAuthenticate;
use Serenity\Operations\EnsureLoginIsNotThrottled;
use Serenity\Operations\PrepareAuthenticatedSession;
use Serenity\Operations\RedirectIfTwoFactorAuthenticatable;
use Serenity\Requests\LoginRequest;
use Serenity\Serenity;

class LoginPipeline
{
  public static function pipe(LoginRequest $request)
  {
    if (Serenity::$authenticateThroughCallback) {
      return (new Pipeline(app()))->send($request)->through(array_filter(
        call_user_func(Serenity::$authenticateThroughCallback, $request)
      ));
    }

    if (is_array(config('serenity.pipelines.login'))) {
      return (new Pipeline(app()))->send($request)->through(array_filter(
        config('serenity.pipelines.login')
      ));
    }

    return (new Pipeline(app()))->send($request)->through(array_filter([
      config('serenity.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
      Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
      AttemptToAuthenticate::class,
      PrepareAuthenticatedSession::class,
    ]));
  }
}
