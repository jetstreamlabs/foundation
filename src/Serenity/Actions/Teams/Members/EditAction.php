<?php

namespace Serenity\Actions\Teams\Members;

use Serenity\Action;
use Serenity\Serenity;

class EditAction extends Action
{
  /**
   * Update the given team member's role.
   *
   * @param  int  $teamId
   * @param  int  $userId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke($teamId, $userId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    $team->users()->findOrFail($userId);

    return view('teams.team-member-role-form', [
      'team' => $team,
      'user' => $team->users()->findOrFail($userId),
      'availableRoles' => array_values(Serenity::$roles),
    ]);
  }
}
