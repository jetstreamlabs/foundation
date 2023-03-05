<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\CreatesTeamsInterface;
use Serenity\Support\Redirection;

class StoreAction extends Action
{
  /**
   * Create a new team.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request)
  {
    $creator = app(CreatesTeamsInterface::class);

    $creator->create($request->user(), $request->all());

    return Redirection::send($creator);
  }
}
