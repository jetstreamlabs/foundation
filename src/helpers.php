<?php

if (! function_exists('bcs')) {
  /**
   * Return the breadcrumb instance from the container
   * or push new crumbs to instance.
   *
   * @param  string|array  $text
   * @param  string  $route
   * @return \App\Services\Breadcrumbs|void
   */
  function bcs($text = null, $route = null)
  {
    // If nothing is passed return the object.
    if (is_null($text) && is_null($route)) {
      return app('breadcrumbs');
    }

    // Array passed, parse it and return.
    if (is_array($text)) {
      foreach ($text as $key => $value) {
        if (is_null($value)) {
          app('breadcrumbs')->add($key);
        } else {
          app('breadcrumbs')->add($key, $value);
        }
      }

      return;
    }

    // No route
    if (is_null($route)) {
      return app('breadcrumbs')->add($text);
    }

    // Normal single add
    app('breadcrumbs')->add($text, $route);
  }
}
