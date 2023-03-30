<?php

namespace Serenity\Foundation;

class Options
{
  /**
   * Create a new middleware option instance.
   *
   * @param  array  $options
   * @return void
   */
  public function __construct(
      protected array &$options
    ) {
  }

  /**
   * Set the action methods the middleware should apply to.
   *
   * @param  array|string|dynamic  $methods
   * @return $this
   */
  public function only($methods)
  {
    $this->options['only'] = is_array($methods) ? $methods : func_get_args();

    return $this;
  }

  /**
   * Set the action methods the middleware should exclude.
   *
   * @param  array|string|dynamic  $methods
   * @return $this
   */
  public function except($methods)
  {
    $this->options['except'] = is_array($methods) ? $methods : func_get_args();

    return $this;
  }
}
