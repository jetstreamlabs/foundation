<?php

namespace Serenity\Responders;

use Illuminate\Validation\ValidationException;
use Serenity\Contracts\FailedPasswordResetLinkRequestInterface;

class FailedPasswordResetLinkRequestResponder implements FailedPasswordResetLinkRequestInterface
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
    if ($request->wantsJson()) {
      throw ValidationException::withMessages([
        'email' => [trans($this->status)],
      ]);
    }

    return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($this->status)]);
  }
}