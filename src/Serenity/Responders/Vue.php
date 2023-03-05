<?php

namespace Serenity\Responders;

use Inertia\ResponseFactory;
use Serenity\Contracts\ResponderInterface;
use Serenity\Responder;

abstract class Vue extends Responder implements ResponderInterface
{
  /**
   * View factory instance.
   *
   * @var \Inertia\ResponseFactory
   */
  protected $view;

  /**
   * The name of the actual Vue component.
   *
   * @var string
   */
  protected $component;

  /**
   * Instantiate the class.
   *
   * @param  \Inertia\ResponseFactory  $factory
   */
  public function __construct(ResponseFactory $factory)
  {
    $this->view = $factory;
    $this->view->setRootView('vue');
  }

  /**
   * Setter for our component.
   *
   * @param  string  $component
   */
  public function setComponent(string $component)
  {
    $this->component = $component;

    return $this;
  }

  public function __call($method, $parameters)
  {
    return $this->view->{$method}(...array_values($parameters));
  }
}
