<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\ProfileInformationUpdated as ProfileInformationUpdatedInterface;
use Serenity\Serenity;

class ProfileInformationUpdated implements ProfileInformationUpdatedInterface
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
      : back()->with('status', Serenity::PROFILE_INFORMATION_UPDATED);
  }
}
