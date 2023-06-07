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
use Jetlabs\Snowflake\Concerns\HasSnowflakePrimary;
use Serenity\Media\AutoProcessMediaTrait;
use Serenity\Media\HasMediaPreviewsTrait;
use Serenity\Media\InteractsWithMedia;
use Serenity\Media\ProcessMediaTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, HasMedia, MustVerifyEmailContract
{
  use Authenticatable;
  use Authorizable;
  use AutoProcessMediaTrait;
  use CanResetPassword;
  use HasMediaPreviewsTrait;
  use HasSnowflakePrimary;
  use HasRoles;
  use InteractsWithMedia;
  use MustVerifyEmail;
  use ProcessMediaTrait;
  use SoftDeletes;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;
}
