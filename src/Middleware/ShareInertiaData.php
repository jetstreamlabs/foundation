<?php

namespace Serenity\Middleware;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Serenity\Foundation\Features;
use Serenity\Serenity;

class ShareInertiaData
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  callable  $next
   * @return \Illuminate\Http\Response
   */
  public function handle($request, $next)
  {
    Inertia::share(array_filter([
      'breadcrumbs' => app('breadcrumbs')->render(),
      'serenityVersion' => Serenity::version(),
      'phpVersion' => PHP_VERSION,
      'flash' => function () use ($request) {
        return [
          'success' => $request->session()->get('success'),
          'error' => $request->session()->get('error'),
          'warning' => $request->session()->get('warning'),
          'info' => $request->session()->get('info'),
          'status' => $request->session()->get('status'),
        ];
      },
      'serenity' => function () use ($request) {
        $user = $request->user();

        return [
          'canCreateTeams' => $user &&
            Serenity::userHasTeamFeatures($user) &&
            Gate::forUser($user)->check('create', Serenity::newTeamModel()),
          'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
          'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
          'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
          'hasEmailVerification' => Features::enabled(Features::emailVerification()),
          'hasAccountDeletionFeatures' => Serenity::hasAccountDeletionFeatures(),
          'hasApiFeatures' => Serenity::hasApiFeatures(),
          'hasTeamFeatures' => Serenity::hasTeamFeatures(),
          'hasTermsAndPrivacyPolicyFeature' => Serenity::hasTermsAndPrivacyPolicyFeature(),
          'managesProfilePhotos' => Serenity::managesProfilePhotos(),
        ];
      },
      'user' => function () use ($request) {
        if (! $user = $request->user()) {
          return;
        }

        $userHasTeamFeatures = Serenity::userHasTeamFeatures($user);

        if ($user && $userHasTeamFeatures) {
          $user->currentTeam;
        }

        return array_merge($user->toArray(), array_filter([
          'permissions' => $user->getPermissionNames(),
          'roles' => $user->getRoleNames(),
          'all_teams' => $userHasTeamFeatures ? $user->allTeams()->values() : null,
        ]), [
          'two_factor_enabled' => ! is_null($user->two_factor_secret),
        ]);
      },
      'errorBags' => function () {
        return collect(optional(Session::get('errors'))->getBags() ?: [])->mapWithKeys(function ($bag, $key) {
          return [$key => $bag->messages()];
        })->all();
      },
    ]));

    return $next($request);
  }
}
