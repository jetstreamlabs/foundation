<?php

namespace Serenity\Routing\PendingRouteTransformers;

use Illuminate\Support\Collection;
use Serenity\Routing\PendingRoutes\PendingRoute;
use Serenity\Routing\PendingRoutes\PendingRouteAction;

class HandleMiddlewareAttribute implements PendingRouteTransformer
{
  /**
   * @param  Collection<PendingRoute>  $pendingRoutes
   * @return Collection<PendingRoute>
   */
  public function transform(Collection $pendingRoutes): Collection
  {
    $pendingRoutes->each(function (PendingRoute $pendingRoute) {
      $pendingRoute->actions->each(function (PendingRouteAction $action) use ($pendingRoute) {
        if ($pendingRouteAttribute = $pendingRoute->getRouteAttribute()) {
          $action->addMiddleware($pendingRouteAttribute->middleware);
        }

        if ($actionRouteAttribute = $action->getRouteAttribute()) {
          $action->addMiddleware($actionRouteAttribute->middleware);
        }
      });
    });

    return $pendingRoutes;
  }
}
