<?php

namespace Serenity\Contracts;

interface Breadcrumbs
{
  /**
   * Add a new breadcrumb to the stack.
   *
   * @param  string  $text
   * @param  string  $route
   * @return \Serenity\Breadcrumbs
   */
  public function add(string $text, string $route = null): Breadcrumbs;

  /**
   * Return the breadcrumbs array.
   *
   * @return array
   */
  public function render(): array;
}
