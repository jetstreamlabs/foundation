<?php

namespace Serenity\Actions\Teams\Members;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\AddsTeamMembers;
use Serenity\Contracts\InvitesTeamMembers;
use Serenity\Features;
use Serenity\Serenity;

class StoreAction extends Action
{
  /**
   * Add a new team member to a team.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $teamId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    if (Features::sendsTeamInvitations()) {
      app(InvitesTeamMembers::class)->invite(
        $request->user(),
        $team,
        $request->email ?: '',
        $request->role
      );
    } else {
      app(AddsTeamMembers::class)->add(
        $request->user(),
        $team,
        $request->email ?: '',
        $request->role
      );
    }

    return back(303);
  }
}
