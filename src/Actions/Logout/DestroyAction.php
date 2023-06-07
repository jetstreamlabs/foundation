<?php

namespace Serenity\Actions\Logout;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Serenity\Contracts\Logout;
use Serenity\Foundation\Action;

class DestroyAction extends Action
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
   * Destroy an authenticated session.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\Logout
   */
  public function __invoke(Request $request): Logout
  {
    $user = $request->user();
    $name = is_null($user->fname) ? $user->username : $user->fname;

    if (! is_null(config('serenity.goodbye'))) {
      $message = [
        'title' => Str::replace('%name%', $name, config('serenity.goodbye.title')),
        'message' => Str::replace('%name%', $name, config('serenity.goodbye.message')),
      ];
    }

    $this->guard->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    if (! is_null(config('serenity.goodbye'))) {
      session()->flash(config('serenity.goodbye.style'), $message);
    }

    return app(Logout::class);
  }
}
