<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Action;
use Serenity\Serenity;

class ShowAction extends Action
{
  /**
   * Show the team management screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @return \Splade\Response
   */
  public function __invoke(Request $request, $teamId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    Gate::authorize('view', $team);

    return view('teams.show', [
      'team' => $team->load('owner', 'users', 'teamInvitations'),
      'availableRoles' => array_values(Serenity::$roles),
      'availablePermissions' => Serenity::$permissions,
      'defaultPermissions' => Serenity::$defaultPermissions,
      'permissions' => [
        'canAddTeamMembers' => Gate::check('addTeamMember', $team),
        'canDeleteTeam' => Gate::check('delete', $team),
        'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
        'canUpdateTeam' => Gate::check('update', $team),
      ],
    ]);
  }
}
