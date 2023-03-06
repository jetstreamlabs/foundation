<?php

namespace Serenity\Routing\PendingRouteTransformers;

use Illuminate\Support\Collection;
use Serenity\Routing\PendingRoutes\PendingRoute;
use Serenity\Routing\PendingRoutes\PendingRouteAction;

class HandleHttpMethodsAttribute implements PendingRouteTransformer
{
  /**
   * @param  Collection<PendingRoute>  $pendingRoutes
   * @return Collection<PendingRoute>
   */
  public function transform(Collection $pendingRoutes): Collection
  {
    $pendingRoutes->each(function (PendingRoute $pendingRoute) {
      $pendingRoute->actions->each(function (PendingRouteAction $action) {
        if (! $routeAttribute = $action->getRouteAttribute()) {
          return;
        }

        if (! $httpMethods = $routeAttribute->methods) {
          return;
        }

        $action->methods = $httpMethods;
      });
    });

    return $pendingRoutes;
  }
}
