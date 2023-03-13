<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Serenity\Contracts\Payload;
use Serenity\Serenity;
use Serenity\Service;

class TeamsService extends Service
{
  public function handle(Request $request, $team): Payload
  {
    if ($request->session()->has('error')) {
      return $this->payloadResponse([
        'message' => $request->session()->get('error'),
        'level' => 'error',
        'team' => $team->load('owner', 'users', 'teamInvitations'),
        'availableRoles' => array_values(Serenity::$roles),
        'availablePermissions' => Serenity::$permissions,
        'defaultPermissions' => Serenity::$defaultPermissions,
        'permissions' => [
          'canAddTeamMembers' => Gate::check('addTeamMember', $team),
          'canDeleteTeam' => Gate::check('delete', $team),
          'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
          'canUpdateTeam' => Gate::check('update', $team),
        ],
      ]);
    }

    return $this->payloadResponse([
      'team' => $team->load('owner', 'users', 'teamInvitations'),
      'availableRoles' => array_values(Serenity::$roles),
      'availablePermissions' => Serenity::$permissions,
      'defaultPermissions' => Serenity::$defaultPermissions,
      'permissions' => [
        'canAddTeamMembers' => Gate::check('addTeamMember', $team),
        'canDeleteTeam' => Gate::check('delete', $team),
        'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
        'canUpdateTeam' => Gate::check('update', $team),
      ],
    ]);
  }
}
