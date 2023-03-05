<?php

namespace Serenity;

use Illuminate\Support\Arr;
use Serenity\Contracts\CriteriaInterface;
use Serenity\Contracts\RepositoryInterface;
use Serenity\Exceptions\EntityNotFound;
use Serenity\Exceptions\NoEntityDefined;

abstract class Repository implements RepositoryInterface, CriteriaInterface
{
  /**
   * Local entity instance.
   *
   * @var object
   */
  protected $entity;

  /**
   * Instantiate the class.
   */
  public function __construct()
  {
    $this->entity = $this->resolveEntity();
  }

  /**
   * Get all of the models from the database.
   *
   * @return static[]
   */
  public function all()
  {
    return $this->entity->get();
  }

  /**
   * Execute a query for a single record by ID.
   *
   * @param  int  $id
   * @return mixed|static
   */
  public function find($id)
  {
    $entity = $this->entity->find($id);

    if (! $entity) {
      throw (new EntityNotFound())->setEntity(
        get_class($this->entity->getEntity()),
        $id
      );
    }

    return $entity;
  }

  /**
   * Execute a query for a single record with soft-deletes by ID.
   *
   * @param  int  $id
   * @return mixed|static
   */
  public function findWithTrashed($id)
  {
    $entity = $this->entity->withTrashed()->find($id);

    if (! $entity) {
      throw (new EntityNotFound())->setEntity(
        get_class($this->entity->getEntity()),
        $id
      );
    }

    return $entity;
  }

  /**
   * Find where by id and value.
   *
   * @param  string  $column
   * @param  mixed  $value
   * @return mixed|static
   */
  public function findWhere($column, $value)
  {
    return $this->entity->where($column, $value)->get();
  }

  /**
   * Find where by array of keys and values.
   *
   * @param  array  $values
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function findWhereMany(array $values)
  {
    return $this->entity->where($values)->get();
  }

  /**
   * Find first instance where by id and value.
   *
   * @param  string  $column
   * @param  mixed  $value
   * @return static
   */
  public function findWhereFirst($column, $value)
  {
    $entity = $this->entity->where($column, $value)->first();

    if (! $entity) {
      throw (new EntityNotFound())->setEntity(
        get_class($this->entity->getEntity())
      );
    }

    return $entity;
  }

  /**
   * Paginate the given query into a simple paginator.
   *
   * @param  int  $perPage
   * @return mixed|static
   */
  public function paginate($perPage = 10)
  {
    return $this->entity->paginate($perPage);
  }

  /**
   * Save a new model and return the instance.
   *
   * @param  array  $properties
   * @return \Illuminate\database\Eloquent\Model|$model
   */
  public function create(array $properties)
  {
    return $this->entity->create($properties);
  }

  /**
   * Update a record in the database.
   *
   * @param  int  $id
   * @param  array  $properties
   * @return int
   */
  public function update($id, array $properties)
  {
    return $this->find($id)->update($properties);
  }

  /**
   * Delete a record from the database.
   *
   * @param  int  $id
   * @return mixed
   */
  public function delete(int $id): bool
  {
    return $this->find($id)->delete();
  }

  /**
   * Force delete a trashed entity.
   *
   * @param  int  $id
   * @return bool
   */
  public function deleteFinal(int $id): bool
  {
    return $this->findWithTrashed($id)->forceDelete();
  }

  /**
   * Restore a soft-deleted entity.
   *
   * @param  int  $id
   * @return bool
   */
  public function restore(int $id): bool
  {
    return $this->findWithTrashed($id)->restore();
  }

  /**
   * Apply criteria to the given entity.
   *
   * @param  array|list  $criteria
   * @return \Serenity\Repository
   */
  public function withCriteria(...$criteria)
  {
    $criteria = Arr::flatten($criteria);

    foreach ($criteria as $criterion) {
      $this->entity = $criterion->apply($this->entity);
    }

    return $this;
  }

  /**
   * Resolve the given entity from the container.
   *
   * @return object
   */
  protected function resolveEntity()
  {
    if (! method_exists($this, 'entity')) {
      throw new NoEntityDefined();
    }

    return app()->make($this->entity());
  }
}
