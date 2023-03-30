<?php

namespace Serenity\Actions\User\Password;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  /**
   * Get the password confirmation status.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function __invoke(Request $request)
  {
    return response()->json([
      'confirmed' => (time() - $request->session()->get('auth.password_confirmed_at', 0)) < $request->input('seconds', config('auth.password_timeout', 900)),
    ]);
  }
}
