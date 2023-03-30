<?php

namespace Serenity\Actions\Teams\Members;

use Illuminate\Http\Request;
use Serenity\Contracts\RemovesTeamMembers;
use Serenity\Foundation\Action;
use Serenity\Serenity;

class DestroyAction extends Action
{
  /**
   * Remove the given user from the given team.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @param  int  $userId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request, $teamId, $userId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    app(RemovesTeamMembers::class)->remove(
      $request->user(),
      $team,
      $user = Serenity::findUserByIdOrFail($userId)
    );

    if ($request->user()->id === $user->id) {
      return redirect(config('serenity.home'));
    }

    return back(303);
  }
}
