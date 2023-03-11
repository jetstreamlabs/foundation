<?php

namespace Serenity\Actions\PrivacyPolicy;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Serenity\Action;
use Serenity\Serenity;

class ShowAction extends Action
{
  /**
   * Show the privacy policy for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function __invoke(Request $request)
  {
    $policyFile = Serenity::localizedMarkdownPath('policy.md');

    return view('policy', [
      'policy' => Str::markdown(file_get_contents($policyFile)),
    ]);
  }
}
