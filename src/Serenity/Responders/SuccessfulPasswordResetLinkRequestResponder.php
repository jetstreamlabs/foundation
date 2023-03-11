<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\SuccessfulPasswordResetLinkRequestInterface;

class SuccessfulPasswordResetLinkRequestResponder implements SuccessfulPasswordResetLinkRequestInterface
{
  /**
   * Create a new response instance.
   *
   * @param  string  $status
   * @return void
   */
  public function __construct(
      protected string $status
    ) {
  }

  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return $request->wantsJson()
      ? new JsonResponse(['message' => trans($this->status)], 200)
      : back()->with('status', trans($this->status));
  }
}
