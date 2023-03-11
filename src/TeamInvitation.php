<?php

namespace Serenity;

use Illuminate\Database\Eloquent\Model;
use Jetlabs\Snowflake\Concerns\HasSnowflakePrimary;

class TeamInvitation extends Model
{
  use HasSnowflakePrimary;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email',
    'role',
  ];

  /**
   * Get the team that the invitation belongs to.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function team()
  {
    return $this->belongsTo(Serenity::teamModel());
  }
}
