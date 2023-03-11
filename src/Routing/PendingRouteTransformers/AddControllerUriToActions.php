<?php

namespace Serenity\Routing\PendingRouteTransformers;

use Illuminate\Support\Collection;
use Serenity\Routing\PendingRoutes\PendingRoute;
use Serenity\Routing\PendingRoutes\PendingRouteAction;

class AddControllerUriToActions implements PendingRouteTransformer
{
  /**
   * @param  Collection<PendingRoute>  $pendingRoutes
   * @return Collection<PendingRoute>
   */
  public function transform(Collection $pendingRoutes): Collection
  {
    $pendingRoutes->each(function (PendingRoute $pendingRoute) {
      $pendingRoute->actions->each(function (PendingRouteAction $action) use ($pendingRoute) {
        $originalActionUri = $action->uri;

        $action->uri = $pendingRoute->uri;

        if ($originalActionUri) {
          $action->uri .= "/{$originalActionUri}";
        }
      });
    });

    return $pendingRoutes;
  }
}
