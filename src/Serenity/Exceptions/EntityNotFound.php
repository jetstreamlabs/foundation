<?php

namespace Serenity\Exceptions;

use Illuminate\Support\Arr;
use RuntimeException;

class EntityNotFound extends RuntimeException
{
  /**
   * Name of the affected Eloquent model.
   *
   * @var string
   */
  protected $entity;

  /**
   * The affected model IDs.
   *
   * @var int|array
   */
  protected $ids;

  /**
   * Set the affected Eloquent model and instance ids.
   *
   * @param  string  $model
   * @param  int|array  $ids
   * @return $this
   */
  public function setEntity($model, $ids = [])
  {
    $this->entity = $model;
    $this->ids = Arr::wrap($ids);

    $this->message = "No query results for entity [{$model}]";

    if (count($this->ids) > 0) {
      $this->message .= ' '.implode(', ', $this->ids);
    } else {
      $this->message .= '.';
    }

    return $this;
  }

  /**
   * Get the affected Eloquent model.
   *
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }

  /**
   * Get the affected Eloquent model IDs.
   *
   * @return int|array
   */
  public function getIds()
  {
    return $this->ids;
  }
}
