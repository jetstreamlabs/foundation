<?php

namespace Serenity\Actions\User\ApiTokens;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Serenity;

class ShowAction extends Action
{
  /**
   * Show the user API token screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Splade\Response
   */
  public function __invoke(Request $request)
  {
    return view('api.index', [
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
