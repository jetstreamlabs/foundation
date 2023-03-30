<?php

namespace Serenity\Actions\TermsOfService;

use Illuminate\Http\Request;
use Serenity\Contracts\TermsOfServiceShow;
use Serenity\Foundation\Action;

class ShowAction extends Action
{
  public function __construct(
      protected TermsOfServiceShow $responder
    ) {
    $this->with('TermsOfService');
  }

  /**
   * Show the terms of service for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    return $this->responder->send();
  }
}
