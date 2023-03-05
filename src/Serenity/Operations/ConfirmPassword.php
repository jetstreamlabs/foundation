<?php

namespace Serenity\Operations;

use Illuminate\Contracts\Auth\StatefulGuard;
use Serenity\Serenity;

class ConfirmPassword
{
  /**
   * Confirm that the given password is valid for the given user.
   *
   * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
   * @param  mixed  $user
   * @param  string|null  $password
   * @return bool
   */
  public function __invoke(StatefulGuard $guard, $user, ?string $password = null)
  {
    $username = Serenity::username();

    return is_null(Serenity::$confirmPasswordsUsingCallback) ? $guard->validate([
      $username => $user->{$username},
      'password' => $password,
    ]) : $this->confirmPasswordUsingCustomCallback($user, $password);
  }

  /**
   * Confirm the user's password using a custom callback.
   *
   * @param  mixed  $user
   * @param  string|null  $password
   * @return bool
   */
  protected function confirmPasswordUsingCustomCallback($user, ?string $password = null)
  {
    return call_user_func(
      Serenity::$confirmPasswordsUsingCallback,
      $user,
      $password
    );
  }
}
