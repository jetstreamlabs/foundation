<?php

namespace Serenity\Actions\User\Profile;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Serenity\Action;
use Serenity\Contracts\UserProfileShow;
use Serenity\Features;
use Serenity\Services\ProfileService;
use Serenity\Support\ConfirmsTwoFactor;
use Serenity\Support\Sessions;

class ShowAction extends Action
{
  public function __construct(
      protected UserProfileShow $responder,
      protected ProfileService $service
    ) {
    bcs([
      __('Profile') => 'last',
    ]);

    $this->with('Profile/Show', true)->serve($service);
  }

  /**
   * Show the general profile settings screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    ConfirmsTwoFactor::validateTwoFactorAuthenticationState($request);

    return $this->responder
      ->make($this->service->handle($request))
      ->send();
  }
}
