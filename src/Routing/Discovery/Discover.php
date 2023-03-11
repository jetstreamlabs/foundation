<?php

namespace Serenity\Routing\Discovery;

class Discover
{
  public static function actions(): DiscoverActions
  {
    return new DiscoverActions();
  }

  public static function views(): DiscoverViews
  {
    return new DiscoverViews();
  }
}
