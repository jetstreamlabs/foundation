<?php

namespace Serenity\Responders;

use Illuminate\Contracts\Support\Responsable;
use Serenity\Contracts\ConfirmPasswordView as ConfirmPasswordViewInterface;
use Serenity\Contracts\LoginView as LoginViewInterface;
use Serenity\Contracts\RegisterView as RegisterViewInterface;
use Serenity\Contracts\RequestPasswordResetLinkView as RequestPasswordResetLinkViewInterface;
use Serenity\Contracts\ResetPasswordView as ResetPasswordViewInterface;
use Serenity\Contracts\TwoFactorChallengeView as TwoFactorChallengeViewInterface;
use Serenity\Contracts\VerifyEmailView as VerifyEmailViewInterface;

class SimpleView implements LoginViewInterface, ResetPasswordViewInterface, RegisterViewInterface, RequestPasswordResetLinkViewInterface, TwoFactorChallengeViewInterface, VerifyEmailViewInterface, ConfirmPasswordViewInterface
{
  /**
   * Create a new response instance.
   *
   * @param  callable|string  $view
   * @return void
   */
  public function __construct(
      protected $view
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
