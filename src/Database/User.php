<?php

namespace Serenity\Database;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Jetlabs\Snowflake\Concerns\HasSnowflakePrimary;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, MustVerifyEmailContract
{
  use Authenticatable;
  use Authorizable;
  use CanResetPassword;
  use HasPermissions;
  use HasRoles;
  use HasSnowflakePrimary;
  use MustVerifyEmail;
  use SoftDeletes;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Get all of the permissions for the user.
   */
  public function getPermissions(): Collection
  {
    return $this->getAllPermissions()->pluck('name');
  }

  /**
   * Get the roles for the user.
   */
  public function getRoles(): Collection
  {
    return $this->getRoleNames();
  }
}
