<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Action;
use Serenity\Contracts\TeamsShow;
use Serenity\Serenity;
use Serenity\Services\TeamsService;

class ShowAction extends Action
{
  public function __construct(
      protected TeamsShow $responder,
      protected TeamsService $service
    ) {
    $this->with('Teams/Show', true);
  }

  /**
   * Show the team management screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $teamId
   * @return \Inertia\Response
   */
  public function __invoke(Request $request, $teamId)
  {
    $team = Serenity::newTeamModel()->findOrFail($teamId);

    Gate::authorize('view', $team);

    app('breadcrumbs')->add('Team Settings', 'last');

    return $this->responder->make(
      $this->service->handle($request, $team)
    )->send();
  }
}
