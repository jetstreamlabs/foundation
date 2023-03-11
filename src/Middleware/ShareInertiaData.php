<?php

namespace Serenity\Middleware;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Serenity\Features;
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
          'flash' => $request->session()->get('flash', []),
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
