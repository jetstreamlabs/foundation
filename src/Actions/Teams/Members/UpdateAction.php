<?php

namespace Serenity\Actions\Teams\Members;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;
use Serenity\Operations\UpdateTeamMemberRole;
use Serenity\Serenity;

class UpdateAction extends Action
{
  /**
   * Update the given team member's role.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @param  int  $userId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $teamId, $userId)
  {
    app(UpdateTeamMemberRole::class)->update(
      $request->user(),
      Serenity::newTeamModel()->findOrFail($teamId),
      $userId,
      $request->role
    );

    return back(303);
  }
}
