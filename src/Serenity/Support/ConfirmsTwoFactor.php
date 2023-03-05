<?php

namespace Serenity\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Serenity\Features;
use Serenity\Operations\DisableTwoFactorAuthentication;

class ConfirmsTwoFactor
{
  /**
   * Validate the two factor authentication state for the request.
   *
   * @param  \Illuminate\Http\Request
   * @return void
   */
  public static function validateTwoFactorAuthenticationState(Request $request)
  {
    if (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
      return;
    }

    $currentTime = time();

    // Notate totally disabled state in session...
    if (self::twoFactorAuthenticationDisabled($request)) {
      $request->session()->put('two_factor_empty_at', $currentTime);
    }

    // If was previously totally disabled this session but is now confirming, notate time...
    if (self::hasJustBegunConfirmingTwoFactorAuthentication($request)) {
      $request->session()->put('two_factor_confirming_at', $currentTime);
    }

    // If the profile is reloaded and is not confirmed but was previously in confirming state, disable...
    if (self::neverFinishedConfirmingTwoFactorAuthentication($request, $currentTime)) {
      app(DisableTwoFactorAuthentication::class)(Auth::user());

      $request->session()->put('two_factor_empty_at', $currentTime);
      $request->session()->remove('two_factor_confirming_at');
    }
  }

  /**
   * Determine if two factor authenticatoin is totally disabled.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return bool
   */
  public static function twoFactorAuthenticationDisabled(Request $request)
  {
    return is_null($request->user()->two_factor_secret) &&
        is_null($request->user()->two_factor_confirmed_at);
  }

  /**
   * Determine if two factor authentication is just now being confirmed within the last request cycle.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return bool
   */
  public static function hasJustBegunConfirmingTwoFactorAuthentication(Request $request)
  {
    return ! is_null($request->user()->two_factor_secret) &&
        is_null($request->user()->two_factor_confirmed_at) &&
        $request->session()->has('two_factor_empty_at') &&
        is_null($request->session()->get('two_factor_confirming_at'));
  }

  /**
   * Determine if two factor authentication was never totally confirmed once confirmation started.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $currentTime
   * @return bool
   */
  public static function neverFinishedConfirmingTwoFactorAuthentication(Request $request, $currentTime)
  {
    return ! array_key_exists('code', $request->session()->getOldInput()) &&
        is_null($request->user()->two_factor_confirmed_at) &&
        $request->session()->get('two_factor_confirming_at', 0) != $currentTime;
  }
}
