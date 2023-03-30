<?php

namespace Serenity\Actions\Email\Verify;

use Illuminate\Http\Request;
use Serenity\Contracts\VerifyEmailView;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class PromptAction extends Action
{
  /**
   * Display the email verification prompt.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\VerifyEmailView
   */
  public function __invoke(Request $request)
  {
    return $request->user()->hasVerifiedEmail()
      ? redirect()->intended(Serenity::redirects('email-verification'))
      : app(VerifyEmailView::class);
  }
}
