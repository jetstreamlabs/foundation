<?php

namespace Serenity\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitingTeamMember
{
  use Dispatchable;

  /**
   * Create a new event instance.
   *
   * @param  mixed  $team
   * @param  mixed  $email
   * @param  mixed  $role
   * @return void
   */
  public function __construct(public $team, public $email, public $role)
  {
  }
}
