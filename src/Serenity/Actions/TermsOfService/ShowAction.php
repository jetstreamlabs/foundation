<?php

namespace Serenity\Actions\TermsOfService;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Serenity\Action;
use Serenity\Serenity;

class ShowAction extends Action
{
  /**
   * Show the terms of service for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Splade\Response
   */
  public function __invoke(Request $request)
  {
    $termsFile = Serenity::localizedMarkdownPath('terms.md');

    return view('terms', [
      'terms' => Str::markdown(file_get_contents($termsFile)),
    ]);
  }
}
