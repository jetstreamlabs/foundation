<?php

namespace Serenity\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
  /**
   * Get the given query scopes.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return void
   */
  public function getQuery(Builder $query);
}
