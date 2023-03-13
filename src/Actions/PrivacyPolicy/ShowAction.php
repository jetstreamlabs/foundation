<?php

namespace Serenity\Actions\PrivacyPolicy;

use Illuminate\Http\Request;
use Serenity\Action;
use Serenity\Contracts\PrivacyPolicyShow;

class ShowAction extends Action
{
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
