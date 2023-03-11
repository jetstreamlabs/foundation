<?php

namespace Serenity\Operations;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Serenity\Events\TeamMemberUpdated;
use Serenity\Rules\Role;
use Serenity\Serenity;

class UpdateTeamMemberRole
{
  /**
   * Update the role for the given team member.
   *
   * @param  mixed  $user
   * @param  mixed  $team
   * @param  int  $teamMemberId
   * @param  string  $role
   * @return void
   */
  public function update($user, $team, $teamMemberId, string $role)
  {
    Gate::forUser($user)->authorize('updateTeamMember', $team);

    Validator::make([
      'role' => $role,
    ], [
      'role' => ['required', 'string', new Role],
    ])->validate();

    $team->users()->updateExistingPivot($teamMemberId, [
      'role' => $role,
    ]);

    TeamMemberUpdated::dispatch($team->fresh(), Serenity::findUserByIdOrFail($teamMemberId));
  }
}
