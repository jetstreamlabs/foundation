<?php

namespace Serenity\Actions\Forgot;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\RequestPasswordResetLinkViewInterface;

class CreateAction extends Action
{
  /**
   * Show the reset password link request view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\RequestPasswordResetLinkViewInterface
   */
  public function __invoke(Request $request): RequestPasswordResetLinkViewInterface
  {
    return app(RequestPasswordResetLinkViewInterface::class);
  }
}
