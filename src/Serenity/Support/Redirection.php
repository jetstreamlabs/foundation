<?php

namespace Serenity\Support;

use Illuminate\Http\Response;

class Redirection
{
  /**
   * Get the redirect response for the given action.
   *
   * @param  mixed  $action
   * @return \Illuminate\Http\Response
   */
  public static function send($action)
  {
    if (method_exists($action, 'redirectTo')) {
      $response = $action->redirectTo();
    } else {
      $response = property_exists($action, 'redirectTo')
        ? $action->redirectTo
        : config('serenity.home');
    }

    return $response instanceof Response ? $response : redirect($response);
  }
}
