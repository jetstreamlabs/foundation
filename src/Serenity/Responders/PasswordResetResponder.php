<?php

namespace Serenity\Responders;

use Illuminate\Http\JsonResponse;
use Serenity\Contracts\PasswordResetInterface;
use Serenity\Serenity;

class PasswordResetResponder implements PasswordResetInterface
{
  /**
   * The response status language key.
   *
   * @var string
   */
  protected $status;

  /**
   * Create a new response instance.
   *
   * @param  string  $status
   * @return void
   */
  public function __construct(string $status)
  {
    $this->status = $status;
  }

  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    return redirect(Serenity::redirects('password-reset', config('serenity.views', true) ? route('login') : null))->with('status', trans($this->status));
  }
}
