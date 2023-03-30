<?php

namespace Serenity\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MuteActions
{
  protected array $ignored = [];

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $action = $request->route()->getAction();

    if (isset($action['controller'])) {
      $class = get_class($request->route()->getController());

      if (! $name = $this->mute($class)) {
        return $next($request);
      }

      throw new \BadMethodCallException(sprintf(
        'Method %s is not allowed in an Action class. See config.serenity.allowed for more info.',
        $name
      ));
    }

    return $next($request);
  }

  protected function ignored()
  {
    $names = [];

    $action = new \ReflectionClass(\Serenity\Foundation\Action::class);
    $methods = $action->getMethods();

    foreach ($methods as $method) {
      $names[] = $method->getName();
    }

    return array_merge(
      $names,
      config('serenity.allowed.actions')
    );
  }

  /**
   * Create a new reflection and check for modifiers.
   *
   * @param  string  $class
   * @return string|bool
   */
  private function mute($class)
  {
    $instance = new \ReflectionClass($class);

    foreach ($instance->getMethods() as $method) {
      if ($method->isPublic() || $method->isProtected()) {
        $name = $method->getName();

        if (! $this->isAllowed($name)) {
          return $name;
        }
      }
    }

    return false;
  }

  /**
   * Pass all our methods through and check if allowed.
   *
   * @param  string  $method
   * @return bool
   */
  private function isAllowed($method)
  {
    if (in_array($method, $this->ignored())) {
      return true;
    }

    return false;
  }
}
