<?php

namespace Serenity\Actions\User\Profile;

use Illuminate\Http\Request;
use Serenity\Contracts\ProfileInformationUpdated;
use Serenity\Contracts\UpdatesUserProfileInformation;
use Serenity\Foundation\Action;

class UpdateAction extends Action
{
  /**
   * Update the user's profile information.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Serenity\Contracts\UpdatesUserProfileInformation  $updater
   * @return \Serenity\Contracts\ProfileInformationUpdated
   */
  public function __invoke(Request $request, UpdatesUserProfileInformation $updater)
  {
    $updater->update($request->user(), $request->all());

    return app(ProfileInformationUpdated::class);
  }
}
