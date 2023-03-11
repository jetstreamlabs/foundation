<?php

namespace Serenity;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
  /**
   * The number of models to return for pagination.
   *
   * @var int
   */
  protected $perPage = 10;

  /**
   * Return self to ensure proper error handling.
   *
   * @return self
   */
  public function getEntity()
  {
    return $this;
  }
}
