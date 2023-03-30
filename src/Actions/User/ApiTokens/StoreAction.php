<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class StoreAction extends Action
{
  /**
   * Create a new API token.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $token = $request->user()->createToken(
      $request->name,
      Serenity::validPermissions($request->input('permissions', []))
    );

    return back()->with('flash', [
      'token' => explode('|', $token->plainTextToken, 2)[1],
    ]);
  }
}
