<?php

namespace Serenity\Operations;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Serenity\Routing\LoginRateLimiter;
use Serenity\Serenity;

class AttemptToAuthenticate
{
  /**
   * The guard implementation.
   *
   * @var \Illuminate\Contracts\Auth\StatefulGuard
   */
  protected $guard;

  /**
   * The login rate limiter instance.
   *
   * @var \Serenity\Routing\LoginRateLimiter
   */
  protected $limiter;

  /**
   * Create a new controller instance.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @param  \Serenity\Routing\LoginRateLimiter  $limiter
   * @return void
   */
  public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
  {
    $this->guard = $guard;
    $this->limiter = $limiter;
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
    if (Serenity::$authenticateUsingCallback) {
      return $this->handleUsingCustomCallback($request, $next);
    }

    $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $credentials = [
      $login => $request->login,
      'password' => $request->password,
    ];

    if ($this->guard->attempt(
      $credentials,
      $request->boolean('remember'))
    ) {
      $user = $request->user();

      $name = is_null($user->fname) ? $user->username : $user->fname;

      if (! is_null(config('serenity.hello'))) {
        $message = [
          'title' => Str::replace('%name%', $name, config('serenity.hello.title')),
          'message' => Str::replace('%name%', $name, config('serenity.hello.message')),
        ];

        session()->flash(config('serenity.hello.style'), $message);
      }

      return $next($request);
    }

    $this->throwFailedAuthenticationException($request);
  }

  /**
   * Attempt to authenticate using a custom callback.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  callable  $next
   * @return mixed
   */
  protected function handleUsingCustomCallback($request, $next)
  {
    $user = call_user_func(Serenity::$authenticateUsingCallback, $request);

    if (! $user) {
      $this->fireFailedEvent($request);

      return $this->throwFailedAuthenticationException($request);
    }

    $this->guard->login($user, $request->boolean('remember'));

    $name = is_null($user->fname) ? $user->username : $user->fname;

    if (! is_null(config('serenity.hello'))) {
      $message = [
        'title' => Str::replace('%name%', $name, config('serenity.hello.title')),
        'message' => Str::replace('%name%', $name, config('serenity.hello.message')),
      ];

      session()->flash(config('serenity.hello.style'), $message);
    }

    return $next($request);
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
   * @return void
   */
  protected function fireFailedEvent($request)
  {
    $login = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    event(new Failed(config('Authenticate.guard'), null, [
      $login => $request->login,
      'password' => $request->password,
    ]));
  }
}
