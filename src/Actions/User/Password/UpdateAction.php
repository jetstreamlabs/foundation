<?php

namespace Serenity\Actions\User\Password;

use Illuminate\Http\Request;
use Serenity\Contracts\PasswordUpdate;
use Serenity\Contracts\UpdatesUserPasswords;
use Serenity\Foundation\Action;

class UpdateAction extends Action
{
  /**
   * Update the user's password.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Contracts\UpdatesUserPasswords  $updater
   * @return \Serenity\Contracts\PasswordUpdate
   */
  public function __invoke(Request $request, UpdatesUserPasswords $updater)
  {
    $updater->update($request->user(), $request->all());

    return app(PasswordUpdate::class);
  }
}
