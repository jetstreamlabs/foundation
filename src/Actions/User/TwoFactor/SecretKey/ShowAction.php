<?php

namespace Serenity\Actions\User\TwoFactor\SecretKey;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  /**
   * Get the current user's two factor authentication setup / secret key.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function __invoke(Request $request)
  {
    if (is_null($request->user()->two_factor_secret)) {
      abort(404, 'Two factor authentication has not been enabled.');
    }

    return response()->json([
      'secretKey' => decrypt($request->user()->two_factor_secret),
    ]);
  }
}
