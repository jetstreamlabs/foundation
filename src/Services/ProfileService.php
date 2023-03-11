<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Serenity\Contracts\Payload;
use Serenity\Features;
use Serenity\Service;
use Serenity\Support\Sessions;

class ProfileService extends Service
{
  public function handle(Request $request): Payload
  {
    if ($request->session()->has('error')) {
      return $this->payloadResponse([
        'message' => $request->session()->get('error'),
        'level' => 'error',
        'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
        'sessions' => Sessions::fetch($request)->all(),
        'breadcrumbs' => app('breadcrumbs')->render(),
      ]);
    }

    return $this->payloadResponse([
      'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
      'sessions' => Sessions::fetch($request)->all(),
      'breadcrumbs' => app('breadcrumbs')->render(),
    ]);
  }
}
