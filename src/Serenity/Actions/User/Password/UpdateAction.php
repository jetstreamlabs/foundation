<?php

namespace Serenity\Actions\User\Password;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\PasswordUpdateInterface;
use Serenity\Contracts\UpdatesUserPasswordsInterface;

class UpdateAction extends Action
{
  /**
   * Update the user's password.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Contracts\UpdatesUserPasswordsInterface  $updater
   * @return \Serenity\Contracts\PasswordUpdateInterface
   */
  public function __invoke(Request $request, UpdatesUserPasswordsInterface $updater)
  {
    $updater->update($request->user(), $request->all());

    return app(PasswordUpdateInterface::class);
  }
}
