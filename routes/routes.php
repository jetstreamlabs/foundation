<?php

use Illuminate\Support\Facades\Route;
use Serenity\Features;
use Serenity\Serenity;

Route::group(['middleware' => config('serenity.middleware', ['web'])], function () {
  $enableViews = config('serenity.views', true);

  // Authentication...
  if ($enableViews) {
    Route::get('/login', \Serenity\Actions\Login\CreateAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('login');
  }

  $limiter = config('serenity.limiters.login');
  $twoFactorLimiter = config('serenity.limiters.two-factor');
  $verificationLimiter = config('serenity.limiters.verification', '6,1');

  Route::post('/login', \Serenity\Actions\Login\StoreAction::class)->middleware(array_filter(['guest:'.config('serenity.auth_guard'), $limiter ? 'throttle:'.$limiter : null]))->name('login.store');
  Route::post('/logout', \Serenity\Actions\Logout\DestroyAction::class)->name('logout');

  // Password Reset...
  if (Features::enabled(Features::resetPasswords())) {
    if ($enableViews) {
      Route::get('/forgot-password', \Serenity\Actions\Forgot\CreateAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('password.request');
      Route::get('/reset-password/{token}', \Serenity\Actions\Reset\CreateAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('password.reset');
    }

    Route::post('/forgot-password', \Serenity\Actions\Forgot\StoreAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('password.email');
    Route::post('/reset-password', \Serenity\Actions\Reset\StoreAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('password.update');
  }

  // Registration...
  if (Features::enabled(Features::registration())) {
    if ($enableViews) {
      Route::get('/register', \Serenity\Actions\Register\CreateAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('register');
    }

    Route::post('/register', \Serenity\Actions\Register\StoreAction::class)->middleware(['guest:'.config('serenity.auth_guard')])->name('register.store');
  }

  // Email Verification...
  if (Features::enabled(Features::emailVerification())) {
    if ($enableViews) {
      Route::get('/email/verify/prompt', \Serenity\Actions\Email\Verify\PromptAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.auth_guard')])->name('verification.notice');
    }

    Route::get('/email/verify/{id}/{hash}', \Serenity\Actions\Email\Verify\VerifyAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.auth_guard'), 'signed', 'throttle:'.$verificationLimiter])->name('verification.verify');
    Route::post('/email/verify/store', \Serenity\Actions\Email\Verify\StoreAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.auth_guard'), 'throttle:'.$verificationLimiter])->name('verification.send');
  }

  // Profile Information...
  if (Features::enabled(Features::updateProfileInformation())) {
    Route::put('/user/profile/update', \Serenity\Actions\User\Profile\UpdateAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')])->name('user-profile-information.update');
  }

  // Passwords...
  if (Features::enabled(Features::updatePasswords())) {
    Route::put('/user/password', \Serenity\Actions\User\Password\UpdateAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')])->name('user-password.update');
  }

  // Password Confirmation...
  if ($enableViews) {
    Route::get('/user/password/confirm', \Serenity\Actions\User\Password\CreateAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')])->name('password.create');
  }

  Route::get('/user/password/status', \Serenity\Actions\User\Password\ShowAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')])->name('password.confirmation');
  Route::post('/user/password/confirm', \Serenity\Actions\User\Password\StoreAction::class)->middleware([config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')])->name('password.confirm');

  // Two Factor Authentication...
  if (Features::enabled(Features::twoFactorAuthentication())) {
    if ($enableViews) {
      Route::get('/two-factor-challenge', \Serenity\Actions\TwoFactor\Challenge\CreateAction::class)->middleware(['guest:'.config('serenity.guard')])->name('two-factor.login');
    }

    Route::post('/two-factor-challenge', \Serenity\Actions\TwoFactor\Challenge\StoreAction::class)->middleware(array_filter(['guest:'.config('serenity.guard'), $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null]))->name('two-factor.challenge');

    $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
      ? [config('serenity.auth_middleware', 'auth').':'.config('serenity.guard'), 'password.confirm']
      : [config('serenity.auth_middleware', 'auth').':'.config('serenity.guard')];

    Route::post('/user/two-factor-authentication', \Serenity\Actions\User\TwoFactor\Authentication\StoreAction::class)->middleware($twoFactorMiddleware)->name('two-factor.enable');
    Route::post('/user/confirmed-two-factor-authentication', \Serenity\Actions\User\TwoFactor\Authentication\UpdateAction::class)->middleware($twoFactorMiddleware)->name('two-factor.confirm');
    Route::delete('/user/two-factor-authentication', \Serenity\Actions\User\TwoFactor\Authentication\DestroyAction::class)->middleware($twoFactorMiddleware)->name('two-factor.disable');
    Route::get('/user/two-factor-qr-code', \Serenity\Actions\User\TwoFactor\QrCode\ShowAction::class)->middleware($twoFactorMiddleware)->name('two-factor.qr-code');
    Route::get('/user/two-factor-secret-key', \Serenity\Actions\User\TwoFactor\SecretKey\ShowAction::class)->middleware($twoFactorMiddleware)->name('two-factor.secret-key');
    Route::get('/user/two-factor-recovery-codes', \Serenity\Actions\User\TwoFactor\Recovery\ShowAction::class)->middleware($twoFactorMiddleware)->name('two-factor.recovery-codes');
    Route::post('/user/two-factor-recovery-codes', \Serenity\Actions\User\TwoFactor\Recovery\StoreAction::class)->middleware($twoFactorMiddleware)->name('two-factor.store');
  }

  if (Serenity::hasTermsAndPrivacyPolicyFeature()) {
    Route::get('/terms-of-service', \Serenity\Actions\TermsOfService\ShowAction::class)->name('terms.show');
    Route::get('/privacy-policy', \Serenity\Actions\PrivacyPolicy\ShowAction::class)->name('policy.show');
  }

  $authMiddleware = config('serenity.guard')
    ? 'auth:'.config('serenity.guard')
    : 'auth';

  $authSessionMiddleware = config('serenity.auth_session', false)
    ? config('serenity.auth_session')
    : null;

  Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
    // User & Profile...
    Route::get('/user/profile', \Serenity\Actions\User\Profile\ShowAction::class)->name('profile.show');
    Route::get('/user/settings', \Serenity\Actions\User\Account\ShowAction::class)->name('settings.show');
    Route::delete('/user/browsers/delete', \Serenity\Actions\User\Browsers\DestroyAction::class)->name('other-browser-sessions.destroy');
    Route::delete('/user/profile-photo', \Serenity\Actions\User\Profile\Photo\DestroyAction::class)->name('current-user-photo.destroy');

    if (Serenity::hasAccountDeletionFeatures()) {
      Route::delete('/user', \Serenity\Actions\User\DestroyAction::class)->name('current-user.destroy');
    }

    Route::group(['middleware' => 'verified'], function () {
      // API...
      if (Serenity::hasApiFeatures()) {
        Route::get('/user/api-tokens', \Serenity\Actions\User\ApiTokens\ShowAction::class)->name('api-tokens.index');
        Route::post('/user/api-tokens', \Serenity\Actions\User\ApiTokens\StoreAction::class)->name('api-tokens.store');
        Route::put('/user/api-tokens/{token}', \Serenity\Actions\User\ApiTokens\UpdateAction::class)->name('api-tokens.update');
        Route::delete('/user/api-tokens/{token}', \Serenity\Actions\User\ApiTokens\DestroyAction::class)->name('api-tokens.destroy');
      }

      // Teams...
      if (Serenity::hasTeamFeatures()) {
        Route::get('/teams/create', \Serenity\Actions\Teams\CreateAction::class)->name('teams.create');
        Route::post('/teams', \Serenity\Actions\Teams\StoreAction::class)->name('teams.store');
        Route::get('/teams/{team}', \Serenity\Actions\Teams\ShowAction::class)->name('teams.show');
        Route::put('/teams/{team}', \Serenity\Actions\Teams\UpdateAction::class)->name('teams.update');
        Route::delete('/teams/{team}', \Serenity\Actions\Teams\DestroyAction::class)->name('teams.destroy');
        Route::put('/current-team', \Serenity\Actions\Teams\Current\UpdateAction::class)->name('current-team.update');
        Route::post('/teams/{team}/members', \Serenity\Actions\Teams\Members\StoreAction::class)->name('team-members.store');
        Route::put('/teams/{team}/members/{user}', \Serenity\Actions\Teams\Members\UpdateAction::class)->name('team-members.update');
        Route::delete('/teams/{team}/members/{user}', \Serenity\Actions\Teams\Members\DestroyAction::class)->name('team-members.destroy');
        Route::get('/teams/invitations/{invitation}', \Serenity\Actions\Teams\Invitations\AcceptAction::class)->middleware(['signed'])->name('team-invitations.accept');
        Route::delete('/teams/invitations/{invitation}', \Serenity\Actions\Teams\Invitations\DestroyAction::class)->name('team-invitations.destroy');
      }
    });
  });
});
