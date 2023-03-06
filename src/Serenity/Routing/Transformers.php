<?php

namespace Serenity\Routing;

use Serenity\Routing\PendingRouteTransformers\AddControllerUriToActions;
use Serenity\Routing\PendingRouteTransformers\AddDefaultRouteName;
use Serenity\Routing\PendingRouteTransformers\HandleDomainAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleDoNotDiscoverAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleFullUriAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleHttpMethodsAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleMiddlewareAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleRouteNameAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleUriAttribute;
use Serenity\Routing\PendingRouteTransformers\HandleUrisOfNestedControllers;
use Serenity\Routing\PendingRouteTransformers\HandleWheresAttribute;
use Serenity\Routing\PendingRouteTransformers\MoveRoutesStartingWithParametersLast;
use Serenity\Routing\PendingRouteTransformers\RejectDefaultControllerMethodRoutes;

class Transformers
{
  /**
   * @return array<class-string>
   */
  public static function defaultRouteTransformers(): array
  {
    return [
      RejectDefaultControllerMethodRoutes::class,
      HandleDoNotDiscoverAttribute::class,
      AddControllerUriToActions::class,
      HandleUrisOfNestedControllers::class,
      HandleRouteNameAttribute::class,
      HandleMiddlewareAttribute::class,
      HandleHttpMethodsAttribute::class,
      HandleUriAttribute::class,
      HandleFullUriAttribute::class,
      HandleWheresAttribute::class,
      AddDefaultRouteName::class,
      HandleDomainAttribute::class,
      MoveRoutesStartingWithParametersLast::class,
    ];
  }
}
