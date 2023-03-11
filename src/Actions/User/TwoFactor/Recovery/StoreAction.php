<?php

namespace Serenity\Actions\User\TwoFactor\Recovery;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\RecoveryCodesGenerated;
use Serenity\Operations\GenerateNewRecoveryCodes;

class StoreAction extends Action
{
  /**
   * Generate a fresh set of two factor authentication recovery codes.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Operations\GenerateNewRecoveryCodes  $generate
   * @return \Serenity\Contracts\RecoveryCodesGeneratedInterface
   */
  public function __invoke(Request $request, GenerateNewRecoveryCodes $generate)
  {
    $generate($request->user());

    return app(RecoveryCodesGenerated::class);
  }
}
