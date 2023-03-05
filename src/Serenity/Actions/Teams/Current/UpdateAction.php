<?php

namespace Serenity\Actions\Teams\Current;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Serenity;

class UpdateAction extends Action
{
  /**
   * Update the authenticated user's current team.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request)
  {
    $team = Serenity::newTeamModel()->findOrFail($request->team_id);

    if (! $request->user()->switchTeam($team)) {
      abort(403);
    }

    return redirect(config('serenity.home'), 303);
  }
}
