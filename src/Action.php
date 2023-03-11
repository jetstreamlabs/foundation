<?php

namespace Serenity;

use Illuminate\Routing\Controller;
use Serenity\Contracts\Action as ActionInterface;
use Serenity\Contracts\Service as ServiceInterface;

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

  /**
   * Set the component file, and payload expectation, then
   * return the class for chaining.
   *
   * @param  string  $component
   * @param  bool  $expects
   * @return \Serenity\Action
   */
  public function with(string $component, bool $expects = false): Action
  {
    $this->responder->setComponent($component)
      ->expectsPayload($expects);

    return $this;
  }

  /**
   * Set the passed in service to the action and then return
   * for chaining.
   *
   * @param  \Serenity\Contracts\Service  $service
   * @return void
   */
  public function serve(ServiceInterface $service)
  {
    $this->service = $service;

    return $this;
  }
}
