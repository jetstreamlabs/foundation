<?php

namespace Serenity\Actions\Email\Verify;

use Illuminate\Auth\Events\Verified;
use Serenity\Action;
use Serenity\Contracts\VerifyEmail;
use Serenity\Requests\VerifyEmailRequest;

class VerifyAction extends Action
{
  /**
   * Mark the authenticated user's email address as verified.
   *
   * @param  \Serenity\Requests\VerifyEmailRequest  $request
   * @return \Serenity\Contracts\VerifyEmail
   */
  public function __invoke(VerifyEmailRequest $request)
  {
    if ($request->user()->hasVerifiedEmail()) {
      return app(VerifyEmail::class);
    }

    if ($request->user()->markEmailAsVerified()) {
      event(new Verified($request->user()));
    }

    return app(VerifyEmail::class);
  }
}
