<?php

namespace Serenity;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Jetlabs\Snowflake\Concerns\HasSnowflakePrimary;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
  use Authenticatable;
  use Authorizable;
  use CanResetPassword;
  use HasSnowflakePrimary;
  use MustVerifyEmail;
  use SoftDeletes;
}
