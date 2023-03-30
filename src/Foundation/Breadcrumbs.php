<?php

namespace Serenity\Foundation;

use Serenity\Contracts\Breadcrumbs as BreadcrumbsContract;

class Breadcrumbs implements BreadcrumbsContract
{
  protected array $breadcrumbs = [];

  /**
   * Add a new breadcrumb to the stack.
   *
   * @param  string  $text
   * @param  string  $route
   * @return \Serenity\Foundation\Breadcrumbs
   */
  public function add(string $text, string $route = null): Breadcrumbs
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
  public function render(): array
  {
    if (request()->user()) {
      $this->breadcrumbs[0] = [
        'text' => __('Dashboard'),
        'route' => route('dashboard'),
      ];
    }

    return $this->breadcrumbs;
  }
}
