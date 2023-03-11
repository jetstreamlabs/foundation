<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\UpdatesTeamNames;
use Serenity\Serenity;

class UpdateAction extends Action
{
  /**
   * Update the given team's name.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $teamId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

    return back(303);
  }
}
