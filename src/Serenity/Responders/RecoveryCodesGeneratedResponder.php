<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\PasswordUpdateInterface;
use Serenity\Serenity;

class RecoveryCodesGeneratedResponder implements PasswordUpdateInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return $request->wantsJson()
      ? new JsonResponse('', 200)
      : back()->with('status', Serenity::RECOVERY_CODES_GENERATED);
  }
}
