<?php

namespace Serenity\Operations;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;
use Serenity\Concerns\TwoFactorAuthenticatable;
use Serenity\Events\TwoFactorAuthenticationChallenged;
use Serenity\Routing\LoginRateLimiter;
use Serenity\Serenity;

class RedirectIfTwoFactorAuthenticatable
{
  /**
   * Create a new controller instance.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @param  \Serenity\Routing\LoginRateLimiter  $limiter
   * @return void
   */
  public function __construct(
    protected StatefulGuard $guard,
    protected LoginRateLimiter $limiter)
  {
  }

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  callable  $next
   * @return mixed
   */
  public function handle($request, $next)
  {
    $user = $this->validateCredentials($request);

    if (Serenity::confirmsTwoFactorAuthentication()) {
      if (optional($user)->two_factor_secret &&
          ! is_null(optional($user)->two_factor_confirmed_at) &&
          in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
        return $this->twoFactorChallengeResponse($request, $user);
      } else {
        return $next($request);
      }
    }

    if (optional($user)->two_factor_secret &&
        in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
      return $this->twoFactorChallengeResponse($request, $user);
    }

    return $next($request);
  }

  /**
   * Attempt to validate the incoming credentials.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return mixed
   */
  protected function validateCredentials($request)
  {
    if (Serenity::$authenticateUsingCallback) {
      return tap(call_user_func(Serenity::$authenticateUsingCallback, $request), function ($user) use ($request) {
        if (! $user) {
          $this->fireFailedEvent($request);

          $this->throwFailedAuthenticationException($request);
        }
      });
    }

    $model = $this->guard->getProvider()->getModel();

    $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    return tap($model::where($login, $request->login)->first(), function ($user) use ($request) {
      if (! $user || ! $this->guard->getProvider()->validateCredentials($user, ['password' => $request->password])) {
        $this->fireFailedEvent($request, $user);

        $this->throwFailedAuthenticationException($request);
      }
    });
  }

  /**
   * Throw a failed authentication validation exception.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  protected function throwFailedAuthenticationException($request)
  {
    $this->limiter->increment($request);

    $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    throw ValidationException::withMessages([
      $login => [trans('auth.failed')],
    ]);
  }

  /**
   * Fire the failed authentication attempt event with the given arguments.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
   * @return void
   */
  protected function fireFailedEvent($request, $user = null)
  {
    $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    event(new Failed(config('Authenticate.guard'), $user, [
      $login => $request->login,
      'password' => $request->password,
    ]));
  }

  /**
   * Get the two factor authentication enabled response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  mixed  $user
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function twoFactorChallengeResponse($request, $user)
  {
    $request->session()->put([
      'login.id' => $user->getKey(),
      'login.remember' => $request->boolean('remember'),
    ]);

    TwoFactorAuthenticationChallenged::dispatch($user);

    return $request->wantsJson()
      ? response()->json(['two_factor' => true])
      : redirect()->route('two-factor.login');
  }
}
