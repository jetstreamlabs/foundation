<?php

namespace Serenity\Routing\PendingRouteTransformers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Serenity\Routing\PendingRoutes\PendingRoute;
use Serenity\Routing\PendingRoutes\PendingRouteAction;

class HandleUriAttribute implements PendingRouteTransformer
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

        if (! $routeAttributeUri = $routeAttribute->uri) {
          return;
        }

        $baseUri = Str::beforeLast($action->uri, '/');
        $action->uri = $baseUri.'/'.$routeAttributeUri;
      });
    });

    return $pendingRoutes;
  }
}
