<?php

namespace Serenity\Routing\Finder;

class Find
{
  public static function actions(): FindActions
  {
    return new FindActions();
  }
}
