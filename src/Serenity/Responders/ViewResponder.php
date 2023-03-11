<?php

namespace Serenity\Responders;

use Inertia\ResponseFactory;
use Serenity\Contracts\ViewResponderInterface;
use Serenity\Responder;

abstract class ViewResponder extends Responder implements ViewResponderInterface
{
  /**
   * The name of the actual Vue component.
   */
  protected string $component;

  /**
   * Instantiate the class.
   *
   * @param  \Inertia\ResponseFactory $view
   */
  public function __construct(
      protected ResponseFactory $view
    ) {
    $this->view->setRootView('app');
  }

  /**
   * Setter for our component.
   *
   * @param  string  $component
   */
  public function setComponent(string $component): ViewResponderInterface
  {
    $this->component = $component;

    return $this;
  }

  public function __call($method, $parameters)
  {
    return $this->view->{$method}(...array_values($parameters));
  }
}
