<?php

namespace Serenity\Support;

use Closure;
use Illuminate\Contracts\Cache\Repository;

class CacheManager
{
  /**
   * Create a new documentation instance.
   *
   * @param  Repository  $cache
   * @return void
   */
  public function __construct(
      protected Repository $cache
    ) {
  }

  /**
   * Wrapper.
   *
   * @param  \Closure  $callback
   * @param  string  $key
   * @return mixed
   */
  public function remember(Closure $callback, $key)
  {
    if (! config('serenity.cache.enabled')) {
      return $callback();
    }

    $cachePeriod = now()->addMinutes(config('serenity.cache.period'));

    return $this->cache->remember(md5($key), $cachePeriod, $callback);
  }
}
