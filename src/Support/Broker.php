<?php

namespace Serenity\Support;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;

class Broker
{
  public static function find(): PasswordBroker
  {
    return Password::broker(config('serenity.passwords'));
  }
}
