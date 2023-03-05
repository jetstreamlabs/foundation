<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\ProfileInformationUpdatedInterface;
use Serenity\Serenity;

class ProfileInformationUpdatedResponder implements ProfileInformationUpdatedInterface
{
  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return back()->with('status', Serenity::PROFILE_INFORMATION_UPDATED);
  }
}
