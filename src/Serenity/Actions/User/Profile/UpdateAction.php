<?php

namespace Serenity\Actions\User\Profile;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\ProfileInformationUpdatedInterface;
use Serenity\Contracts\UpdatesUserProfileInformationInterface;

class UpdateAction extends Action
{
  /**
   * Update the user's profile information.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Contracts\UpdatesUserProfileInformationInterface  $updater
   * @return \Serenity\Contracts\ProfileInformationUpdatedInterface
   */
  public function __invoke(Request $request, UpdatesUserProfileInformationInterface $updater)
  {
    $updater->update($request->user(), $request->all());

    return app(ProfileInformationUpdatedInterface::class);
  }
}
