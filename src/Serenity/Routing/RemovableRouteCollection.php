<?php

namespace Serenity\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Arr;

class RemovableRouteCollection extends RouteCollection
{
  /**
   * Clone a base route collection into an removable instance
   *
   * @param  \Illuminate\Routing\RouteCollection  $base
   * @return \App\Routing\RouteCollection
   */
  public static function cloneFrom(RouteCollection $base)
  {
    $clone = new static();

    $clone->routes = $base->routes;
    $clone->allRoutes = $base->allRoutes;
    $clone->nameList = $base->nameList;
    $clone->actionList = $base->actionList;

    return $clone;
  }

  /**
   * Remove a Route instance from the collection by uri for any method
   *
   * @param  mixed  $methods
   * @param  string  $uri
   * @return static
   */
  public function remove($methods, string $uri)
  {
    foreach ($this->routes as $method => $routes) {
      if (! in_array($method, Arr::wrap($methods))) {
        continue;
      }

      foreach ($routes as $domainAndUri => $route) {
        if (trim($uri, '/') !== $route->uri()) {
          continue;
        }

        $this->removeRoute($route, $method, $domainAndUri);
      }
    }

    return $this;
  }

  /**
   * Remove all matching routes for the given method
   *
   * @param  \Illuminate\Routing\Route  $route
   * @param  string  $method
   * @param  string  $domainAndUri
   * @return void
   */
  protected function removeRoute(Route $route, string $method, string $domainAndUri)
  {
    unset($this->routes[$method][$domainAndUri]);
    unset($this->allRoutes[$method.$domainAndUri]);

    if ($name = $route->getName()) {
      unset($this->nameList[$name]);
    }

    $action = $route->getAction();

    if (isset($action['controller'])) {
      unset($this->actionList[trim($action['controller'], '\\')]);
    }
  }
}
