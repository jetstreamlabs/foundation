<?php

use Serenity\Settings\GeneralSettings;

if (! function_exists('bcs')) {
  /**
   * Return the breadcrumb instance from the container
   * or push new crumbs to instance.
   *
   * @return \Serenity\Contracts\Breadcrumbs|void
   */
  function bcs(mixed $text = null, string $route = null): mixed
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

      return app('breadcrumbs');
    }

    // No route
    if (is_null($route)) {
      return app('breadcrumbs')->add($text);
    }

    // Normal single add
    return app('breadcrumbs')->add($text, $route);
  }
}

if (! function_exists('___')) {
  function ___($group, $key, $params = [], $locale = null)
  {
    return trans($group.'.'.$key, $params, $locale);
  }

  function ___ch($group, $key, $number, $params = [], $locale = null)
  {
    return trans_choice($group.'.'.$key, $number, $params, $locale);
  }
}

if (! function_exists('getAvailableLocalesTranslated')) {
  function getAvailableLocalesTranslated()
  {
    return collect(app(GeneralSettings::class)->available_locales)->map(function ($locale) {
      return [
        'key' => $locale,
        'value' => trans("locales.admin.$locale"),
      ];
    })->all();
  }
}
