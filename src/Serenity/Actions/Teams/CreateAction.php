<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Action;
use Serenity\Serenity;

class CreateAction extends Action
{
  /**
   * Show the team creation screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    Gate::authorize('create', Serenity::newTeamModel());

    return view('teams.create');
  }
}
