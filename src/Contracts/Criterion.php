<?php

namespace Serenity\Contracts;

interface Criterion
{
  /**
   * Apply the requirements to the entity.
   *
   * @param  object  $entity
   */
  public function apply($entity);
}
