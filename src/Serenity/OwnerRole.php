<?php

namespace Serenity;

class OwnerRole extends Role
{
  /**
   * Create a new role instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct('owner', 'Owner', ['*']);
  }
}
