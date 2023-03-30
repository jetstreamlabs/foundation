<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class UpdateAction extends Action
{
  /**
   * Update the given API token's permissions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $tokenId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $tokenId)
  {
    $request->validate([
      'permissions' => 'array',
      'permissions.*' => 'string',
    ]);

    $token = $request->user()->tokens()->where('id', $tokenId)->firstOrFail();

    $token->forceFill([
      'abilities' => Serenity::validPermissions($request->input('permissions', [])),
    ])->save();

    return back(303);
  }
}
