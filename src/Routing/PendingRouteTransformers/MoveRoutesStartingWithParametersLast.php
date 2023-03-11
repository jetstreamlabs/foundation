<?php

namespace Serenity\Routing\PendingRouteTransformers;

use Illuminate\Support\Collection;
use Serenity\Routing\PendingRoutes\PendingRoute;
use Serenity\Routing\PendingRoutes\PendingRouteAction;

class MoveRoutesStartingWithParametersLast implements PendingRouteTransformer
{
  /**
   * @param  Collection<PendingRoute>  $pendingRoutes
   * @return Collection<PendingRoute>
   */
  public function transform(Collection $pendingRoutes): Collection
  {
    return $pendingRoutes->sortBy(function (PendingRoute $pendingRoute) {
      $containsRouteStartingWithUri = $pendingRoute->actions->contains(function (PendingRouteAction $action) {
        return str_starts_with($action->uri, '{');
      });

      if (! $containsRouteStartingWithUri) {
        return 0;
      }

      return $pendingRoute->actions->max(function (PendingRouteAction $action) {
        if (! str_starts_with($action->uri, '{')) {
          return PHP_INT_MAX;
        }

        return PHP_INT_MAX - count(explode('/', $action->uri));
      });
    })
        ->values();
  }
}
