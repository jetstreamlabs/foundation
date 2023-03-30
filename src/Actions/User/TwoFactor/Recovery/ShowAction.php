<?php

namespace Serenity\Actions\User\TwoFactor\Recovery;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  /**
   * Get the two factor authentication recovery codes for authenticated user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    if (! $request->user()->two_factor_secret ||
        ! $request->user()->two_factor_recovery_codes) {
      return [];
    }

    return response()->json(json_decode(decrypt(
      $request->user()->two_factor_recovery_codes
    ), true));
  }
}
