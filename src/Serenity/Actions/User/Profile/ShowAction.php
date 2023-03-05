<?php

namespace Serenity\Actions\User\Profile;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Features;
use Serenity\Support\ConfirmsTwoFactor;
use Serenity\Support\Sessions;

class ShowAction extends Action
{
  /**
   * Show the general profile settings screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Splade\Response
   */
  public function __invoke(Request $request)
  {
    ConfirmsTwoFactor::validateTwoFactorAuthenticationState($request);

    return view('profile.show', [
      'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
      'sessions' => Sessions::fetch($request)->all(),
    ]);
  }
}
