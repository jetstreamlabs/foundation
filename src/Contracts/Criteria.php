<?php

namespace Serenity\Contracts;

interface Criteria
{
  /**
   * Apply criteria to the given entity.
   *
   * @param  array  $criteria
   */
  public function withCriteria(...$criteria);
}
