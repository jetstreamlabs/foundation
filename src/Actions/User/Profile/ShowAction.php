<?php

namespace Serenity\Actions\User\Profile;

use Illuminate\Http\Request;
use Serenity\Contracts\UserProfileShow;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  public function __construct(
      protected UserProfileShow $responder
    ) {
    $this->with('Profile/Show');

    bcs(__('Profile'), 'last');
  }

  /**
   * Show the general profile settings screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    return $this->responder->send();
  }
}
