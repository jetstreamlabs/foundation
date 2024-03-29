<?php

namespace Serenity\Actions\PrivacyPolicy;

use Illuminate\Http\Request;
use Serenity\Contracts\PrivacyPolicyShow;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  /**
   * Instantiate the action.
   *
   * @param  \Serenity\Contracts\PrivacyPolicyShow  $responder
   */
  public function __construct(
     protected PrivacyPolicyShow $responder
    ) {
    $this->with('PrivacyPolicy');
  }

  /**
   * Show the privacy policy for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    return $this->responder->send();
  }
}
