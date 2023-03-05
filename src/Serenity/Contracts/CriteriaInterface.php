<?php

namespace Serenity\Contracts;

interface CriteriaInterface
{
  /**
   * Apply criteria to the given entity.
   *
   * @param  array  $criteria
   */
  public function withCriteria(...$criteria);
}
