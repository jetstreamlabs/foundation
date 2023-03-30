<?php

namespace Serenity\Actions\Teams\Invitations;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class DestroyAction extends Action
{
  /**
   * Cancel the given team invitation.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $invitationId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $invitationId)
  {
    $model = Serenity::teamInvitationModel();

    $invitation = $model::whereKey($invitationId)->firstOrFail();

    if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
      throw new AuthorizationException;
    }

    $invitation->delete();

    return back(303);
  }
}
