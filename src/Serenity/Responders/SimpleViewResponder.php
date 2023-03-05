<?php

namespace Serenity\Responders;

use Illuminate\Contracts\Support\Responsable;
use Serenity\Contracts\ConfirmPasswordViewInterface;
use Serenity\Contracts\LoginViewInterface;
use Serenity\Contracts\RegisterViewInterface;
use Serenity\Contracts\RequestPasswordResetLinkViewInterface;
use Serenity\Contracts\ResetPasswordViewInterface;
use Serenity\Contracts\TwoFactorChallengeViewInterface;
use Serenity\Contracts\VerifyEmailViewInterface;

class SimpleViewResponder implements LoginViewInterface, ResetPasswordViewInterface, RegisterViewInterface, RequestPasswordResetLinkViewInterface, TwoFactorChallengeViewInterface, VerifyEmailViewInterface, ConfirmPasswordViewInterface
{
  /**
   * The name of the view or the callable used to generate the view.
   *
   * @var callable|string
   */
  protected $view;

  /**
   * Create a new response instance.
   *
   * @param  callable|string  $view
   * @return void
   */
  public function __construct($view)
  {
    $this->view = $view;
  }

  /**
   * Create an HTTP response that represents the object.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function toResponse($request)
  {
    if (! is_callable($this->view) || is_string($this->view)) {
      return view($this->view, ['request' => $request]);
    }

    $response = call_user_func($this->view, $request);

    if ($response instanceof Responsable) {
      return $response->toResponse($request);
    }

    return $response;
  }
}
