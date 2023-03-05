<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\DeletesTeamsInterface;
use Serenity\Operations\ValidateTeamDeletion;
use Serenity\Serenity;
use Serenity\Support\Redirection;

class DestroyAction extends Action
{
  /**
   * Delete the given team.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $teamId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    app(ValidateTeamDeletion::class)->validate($request->user(), $team);

    $deleter = app(DeletesTeamsInterface::class);

    $deleter->delete($team);

    return Redirection::send($deleter);
  }
}
