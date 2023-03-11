<?php

namespace Serenity\Contracts;

interface Action
{
  /**
   * Register middleware on the action.
   *
   * @param  array|string|\Closure  $middleware
   * @param  array  $options
   * @return \Serenity\Options
   */
  public function middleware($middleware, array $options = []);

  /**
   * Get the middleware assigned to the action.
   *
   * @return array
   */
  public function getMiddleware();

  /**
   * Execute an action on the controller.
   *
   * @param  string  $method
   * @param  array  $parameters
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function callAction($method, $parameters);

  /**
   * Handle calls to missing methods on the action.
   *
   * @param  string  $method
   * @param  array  $parameters
   * @return mixed
   *
   * @throws \BadMethodCallException
   */
  public function __call($method, $parameters);
}
