<?php

namespace Serenity\Concerns;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait SelfResolves
{
  /**
   * Resolves model regardless of given identifier.
   *
   * @param $model
   * @param  bool  $withTrashed
   * @return mixed
   *
   * @throws \Exception
   */
  public static function resolveSelf($model, $withTrashed = false)
  {
    $className = get_called_class();

    if (is_null($model)) {
      return null;
    }

    if (! $model instanceof $className) {
      if (is_numeric($model)) {
        try {
          $model = $className::when($withTrashed, function ($query) {
            return $query->withTrashed();
          })->findOrFail($model);
        } catch (ModelNotFoundException $e) {
          throw new Exception($className.' not found with the given ID.');
        }
      } else {
        try {
          $model = $className::when($withTrashed, function ($query) {
            return $query->withTrashed();
          })->where('slug', $model)->firstOrFail();
        } catch (ModelNotFoundException $e) {
          throw new Exception($className.' not found with the given slug.');
        }
      }
    }

    return $model;
  }
}
