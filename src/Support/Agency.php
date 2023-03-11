<?php

namespace Serenity\Support;

use Jenssegers\Agent\Agent;

class Agency
{
  public static function create($session)
  {
    return tap(new Agent, function ($agent) use ($session) {
      $agent->setUserAgent($session->user_agent);
    });
  }
}
