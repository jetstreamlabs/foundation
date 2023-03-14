<?php

namespace Serenity\Support;

use Illuminate\Support\Facades\Facade;
use Serenity\Contracts\ContractMapper;

class ContractBinder extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   *
   * @throws \RuntimeException
   */
  protected static function getFacadeAccessor()
  {
    return ContractMapper::class;
  }
}
