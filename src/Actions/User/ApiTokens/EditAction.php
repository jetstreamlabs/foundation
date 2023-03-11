<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Serenity;

class EditAction extends Action
{
  /**
   * Edit the given API token's permissions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $tokenId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $tokenId)
  {
    $token = $request->user()->tokens()->where('id', $tokenId)->firstOrFail();

    return view('api.edit', [
      'token' => $token,
      'availablePermissions' => Serenity::$permissions,
    ]);
  }
}
