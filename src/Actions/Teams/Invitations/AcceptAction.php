<?php

namespace Serenity\Actions\Teams\Invitations;

use Illuminate\Http\Request;
use Serenity\Contracts\AddsTeamMembers;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class AcceptAction extends Action
{
  /**
   * Accept a team invitation.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $invitationId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $invitationId)
  {
    $model = Serenity::teamInvitationModel();

    $invitation = $model::whereKey($invitationId)->firstOrFail();

    app(AddsTeamMembers::class)->add(
      $invitation->team->owner,
      $invitation->team,
      $invitation->email,
      $invitation->role
    );

    $invitation->delete();

    return redirect(config('serenity.home'))->banner(
      __('Great! You have accepted the invitation to join the :team team.', ['team' => $invitation->team->name]),
    );
  }
}
