<?php

namespace Serenity\Contracts;

interface CreatesNewUsersInterface
{
  /**
   * Validate and create a newly registered user.
   *
   * @param  array  $input
   * @return \Illuminate\Foundation\Auth\User
   */
  public function create(array $input);
}
