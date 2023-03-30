<?php

namespace Serenity\Actions\Forgot;

use Illuminate\Http\Request;
use Serenity\Contracts\RequestPasswordResetLinkView;
use Serenity\Foundation\Action;

class CreateAction extends Action
{
  /**
   * Show the reset password link request view.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Serenity\Contracts\RequestPasswordResetLinkView
   */
  public function __invoke(Request $request): RequestPasswordResetLinkView
  {
    return app(RequestPasswordResetLinkView::class);
  }
}
