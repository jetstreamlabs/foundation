<?php

namespace Serenity\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Serenity\Contracts\Filter as FilterInterface;

abstract class Filter implements FilterInterface
{
  /**
   * Create a new instance of the class.
   *
   * @param  \Illuminate\Http\Request  $request
   */
  public function __construct(
      protected Request $request
    ) {
  }

  /**
   * Abstract get query class required by filter classes.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return void
   */
  abstract public function getQuery(Builder $query);

  /**
   * Filter the query by soft deletes.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return void
   */
  protected function filterByTrashed(Builder $query)
  {
    $query->when($this->request->filled('trashed'), function ($query) {
      if ($this->request->input('trashed') === 'with') {
        $query->withTrashed();
      }

      if ($this->request->input('trashed') === 'only') {
        $query->onlyTrashed();
      }
    });
  }
}
