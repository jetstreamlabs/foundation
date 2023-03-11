<?php

namespace Serenity\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingTeamMember
{
  use Dispatchable;

  /**
   * Create a new event instance.
   *
   * @param  mixed  $team
   * @param  mixed  $user
   * @return void
   */
  public function __construct(public $team, public $user)
  {
  }
}
