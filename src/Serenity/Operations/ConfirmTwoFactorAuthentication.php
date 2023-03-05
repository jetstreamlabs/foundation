<?php

namespace Serenity\Operations;

use Illuminate\Validation\ValidationException;
use Serenity\Contracts\TwoFactorAuthenticationProviderInterface;
use Serenity\Events\TwoFactorAuthenticationConfirmed;

class ConfirmTwoFactorAuthentication
{
  /**
   * The two factor authentication provider.
   *
   * @var \Serenity\Contracts\TwoFactorAuthenticationProviderInterface
   */
  protected $provider;

  /**
   * Create a new action instance.
   *
   * @param  \Serenity\Contracts\TwoFactorAuthenticationProviderInterface  $provider
   * @return void
   */
  public function __construct(TwoFactorAuthenticationProviderInterface $provider)
  {
    $this->provider = $provider;
  }

  /**
   * Confirm the two factor authentication configuration for the user.
   *
   * @param  mixed  $user
   * @param  string  $code
   * @return void
   */
  public function __invoke($user, $code)
  {
    if (empty($user->two_factor_secret) ||
        empty($code) ||
        ! $this->provider->verify(decrypt($user->two_factor_secret), $code)) {
      throw ValidationException::withMessages([
        'code' => [__('The provided two factor authentication code was invalid.')],
      ])->errorBag('confirmTwoFactorAuthentication');
    }

    $user->forceFill([
      'two_factor_confirmed_at' => now(),
    ])->save();

    TwoFactorAuthenticationConfirmed::dispatch($user);
  }
}
