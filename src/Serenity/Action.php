<?php

namespace Serenity;

use Illuminate\Routing\Controller;
use Serenity\Contracts\ActionInterface;

abstract class Action extends Controller implements ActionInterface
{
  /**
   * Register middleware on the controller.
   *
   * @param  \Closure|array|string  $middleware
   * @param  array  $options
   * @return \Illuminate\Routing\ControllerMiddlewareOptions
   */
  public function middleware($middleware, array $options = [])
  {
    foreach ((array) $middleware as $m) {
      $this->middleware[] = [
        'middleware' => $m,
        'options' => &$options,
      ];
    }

    return new Options($options);
  }
}
