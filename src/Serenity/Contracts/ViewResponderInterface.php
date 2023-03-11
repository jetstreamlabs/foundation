<?php

namespace Serenity\Contracts;

use Serenity\Contracts\ResponderInterface;

interface ViewResponderInterface extends ResponderInterface
{
  /**
   * Build up a response and return it to our action.
   *
   * @return \Inertia\ResponseFactory
   */
  public function send();

  /**
   * Redirect response for Vue components.
   *
   * @return \Illuminate\Http\Response
   */
  public function replace();
}
