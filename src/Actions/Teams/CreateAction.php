<?php

namespace Serenity\Actions\Teams;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Action;
use Serenity\Contracts\TeamsCreate;
use Serenity\Serenity;

class CreateAction extends Action
{
  public function __construct(
      protected TeamsCreate $responder
    ) {
    $this->with('Teams/Create');

    app('breadcrumbs')->add('Create a New Team', 'last');
  }

  /**
   * Show the team creation screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    Gate::authorize('create', Serenity::newTeamModel());

    return $this->responder->send();
  }
}
