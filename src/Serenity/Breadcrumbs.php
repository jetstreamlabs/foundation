<?php

namespace Serenity;

class Breadcrumbs
{
  protected array $breadcrumbs = [];

  /**
   * Add a new breadcrumb to the stack.
   *
   * @param  string  $text
   * @param  string  $route
   */
  public function add($text, $route = null)
  {
    $this->breadcrumbs[] = [
      'text' => $text,
      'route' => ! is_null($route) ? $route : 'last',
    ];

    return $this;
  }

  /**
   * Return the breadcrumbs array.
   *
   * @return array
   */
  public function render()
  {
    return $this->breadcrumbs;
  }
}
