<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;

class DestroyAction extends Action
{
  /**
   * Delete the given API token.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $tokenId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $tokenId)
  {
    $request->user()->tokens()->where('id', $tokenId)->first()->delete();

    return back(303);
  }
}
