<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Serenity\Contracts\Payload;
use Serenity\Serenity;

class ApiTokenService extends Service
{
  public function handle(Request $request): Payload
  {
    if ($request->session()->has('error')) {
      return $this->payloadResponse([
        'message' => $request->session()->get('error'),
        'level' => 'error',
        'tokens' => $request->user()->tokens->map(function ($token) {
          return $token->toArray() + [
            'last_used_ago' => optional($token->last_used_at)->diffForHumans(),
          ];
        }),
        'availablePermissions' => Serenity::$permissions,
        'defaultPermissions' => Serenity::$defaultPermissions,
      ]);
    }

    return $this->payloadResponse([
      'tokens' => $request->user()->tokens->map(function ($token) {
        return $token->toArray() + [
          'last_used_ago' => optional($token->last_used_at)->diffForHumans(),
        ];
      }),
      'availablePermissions' => Serenity::$permissions,
      'defaultPermissions' => Serenity::$defaultPermissions,
    ]);
  }
}
