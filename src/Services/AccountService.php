<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Serenity\Contracts\Payload;
use Serenity\Features;
use Serenity\Service;
use Serenity\Support\ConfirmsTwoFactor;
use Serenity\Support\Sessions;

class AccountService extends Service
{
  public function handle(Request $request): Payload
  {
    ConfirmsTwoFactor::validateTwoFactorAuthenticationState($request);

    if ($request->session()->has('error')) {
      return $this->payloadResponse([
        'message' => $request->session()->get('error'),
        'level' => 'error',
        'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
        'sessions' => Sessions::fetch($request)->all(),
      ]);
    }

    return $this->payloadResponse([
      'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
      'sessions' => Sessions::fetch($request)->all(),
    ]);
  }
}
