<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Serenity\Contracts\CreatesTeams;
use Serenity\Foundation\Action;
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
    $creator = app(CreatesTeams::class);

    $creator->create($request->user(), $request->all());

    return Redirection::send($creator);
  }
}
