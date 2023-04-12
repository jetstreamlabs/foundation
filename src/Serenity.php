<?php

namespace Serenity;

use Illuminate\Support\Arr;
use Serenity\Concerns\HasTeams;
use Serenity\Contracts\AddsTeamMembers;
use Serenity\Contracts\ConfirmPasswordView;
use Serenity\Contracts\CreatesNewUsers;
use Serenity\Contracts\CreatesTeams;
use Serenity\Contracts\DeletesTeams;
use Serenity\Contracts\DeletesUsers;
use Serenity\Contracts\InvitesTeamMembers;
use Serenity\Contracts\LoginView;
use Serenity\Contracts\RegisterView;
use Serenity\Contracts\RemovesTeamMembers;
use Serenity\Contracts\RequestPasswordResetLinkView;
use Serenity\Contracts\ResetPasswordView;
use Serenity\Contracts\ResetsUserPasswords;
use Serenity\Contracts\TwoFactorChallengeView;
use Serenity\Contracts\UpdatesTeamNames;
use Serenity\Contracts\UpdatesUserPasswords;
use Serenity\Contracts\UpdatesUserProfileInformation;
use Serenity\Contracts\VerifyEmailView;
use Serenity\Foundation\Features;
use Serenity\Foundation\InertiaManager;
use Serenity\Foundation\Role;
use Serenity\Responders\SimpleView;

class Serenity
{
  public static bool $registersRoutes = true;

  public static array $roles = [];

  public static array $permissions = [];

  public static array $defaultPermissions = [];

  public static string $userModel = 'App\\Domain\\Models\\User';

  public static string $teamModel = 'App\\Domain\\Models\\Team';

  public static string $membershipModel = 'App\\Domain\\Models\\Membership';

  public static string $teamInvitationModel = 'App\\Domain\\Models\\TeamInvitation';

  /**
   * The folllwing are all callable or null;
   *
   * @var callable|null
   */
  public static $authenticateThroughCallback;

  public static $authenticateUsingCallback;

  public static $confirmPasswordsUsingCallback;

  /**
   * The Inertia manager instance.
   *
   * @var \Serenity\Foundation\InertiaManager
   */
  public static $inertiaManager;

  const PASSWORD_UPDATED = 'password-updated';

  const PROFILE_INFORMATION_UPDATED = 'profile-information-updated';

  const RECOVERY_CODES_GENERATED = 'recovery-codes-generated';

  const TWO_FACTOR_AUTHENTICATION_CONFIRMED = 'two-factor-authentication-confirmed';

  const TWO_FACTOR_AUTHENTICATION_DISABLED = 'two-factor-authentication-disabled';

  const TWO_FACTOR_AUTHENTICATION_ENABLED = 'two-factor-authentication-enabled';

  const VERIFICATION_LINK_SENT = 'verification-link-sent';

  /**
   * Render the current foundation version.
   *
   * @return void
   */
  public static function version()
  {
    return '2.3.4';
  }

  /**
   * Return the base path of the application.
   *
   * @param  string  $path
   * @return string
   */
  public static function basePath($path = ''): string
  {
    return app()->basePath($path);
  }

  /**
   * Determine if Serenity has registered roles.
   *
   * @return bool
   */
  public static function hasRoles()
  {
    return count(static::$roles) > 0;
  }

  /**
   * Find the role with the given key.
   *
   * @param  string  $key
   * @return \Serenity\Role
   */
  public static function findRole(string $key)
  {
    return static::$roles[$key] ?? null;
  }

  /**
   * Define a role.
   *
   * @param  string  $key
   * @param  string  $name
   * @param  array  $permissions
   * @return \Serenity\Role
   */
  public static function role(string $key, string $name, array $permissions)
  {
    static::$permissions = collect(array_merge(static::$permissions, $permissions))
      ->unique()
      ->sort()
      ->values()
      ->all();

    return tap(new Role($key, $name, $permissions), function ($role) use ($key) {
      static::$roles[$key] = $role;
    });
  }

  /**
   * Determine if any permissions have been registered with Serenity.
   *
   * @return bool
   */
  public static function hasPermissions()
  {
    return count(static::$permissions) > 0;
  }

  /**
   * Define the available API token permissions.
   *
   * @param  array  $permissions
   * @return static
   */
  public static function permissions(array $permissions)
  {
    static::$permissions = $permissions;

    return new static;
  }

  /**
   * Define the default permissions that should be available to new API tokens.
   *
   * @param  array  $permissions
   * @return static
   */
  public static function defaultApiTokenPermissions(array $permissions)
  {
    static::$defaultPermissions = $permissions;

    return new static;
  }

  /**
   * Return the permissions in the given list that are actually defined permissions for the application.
   *
   * @param  array  $permissions
   * @return array
   */
  public static function validPermissions(array $permissions)
  {
    return array_values(array_intersect($permissions, static::$permissions));
  }

  /**
   * Determine if Serenity is managing profile photos.
   *
   * @return bool
   */
  public static function managesProfilePhotos()
  {
    return Features::managesProfilePhotos();
  }

  /**
   * Determine if Serenity is supporting API features.
   *
   * @return bool
   */
  public static function hasApiFeatures()
  {
    return Features::hasApiFeatures();
  }

  /**
   * Determine if Serenity is supporting team features.
   *
   * @return bool
   */
  public static function hasTeamFeatures()
  {
    return Features::hasTeamFeatures();
  }

  /**
   * Determine if a given user model utilizes the "HasTeams" trait.
   *
   * @param  \Illuminate\Database\Eloquent\Model
   * @return bool
   */
  public static function userHasTeamFeatures($user)
  {
    return (array_key_exists(HasTeams::class, class_uses_recursive($user)) ||
      method_exists($user, 'currentTeam')) &&
      static::hasTeamFeatures();
  }

  /**
   * Determine if the application is using the terms confirmation feature.
   *
   * @return bool
   */
  public static function hasTermsAndPrivacyPolicyFeature()
  {
    return Features::hasTermsAndPrivacyPolicyFeature();
  }

  /**
   * Determine if the application is using any account deletion features.
   *
   * @return bool
   */
  public static function hasAccountDeletionFeatures()
  {
    return Features::hasAccountDeletionFeatures();
  }

  /**
   * Find a user instance by the given ID.
   *
   * @param  int  $id
   * @return mixed
   */
  public static function findUserByIdOrFail($id)
  {
    return static::newUserModel()->where('id', $id)->firstOrFail();
  }

  /**
   * Find a user instance by the given email address or fail.
   *
   * @param  string  $email
   * @return mixed
   */
  public static function findUserByEmailOrFail(string $email)
  {
    return static::newUserModel()->where('email', $email)->firstOrFail();
  }

  /**
   * Get the name of the user model used by the application.
   *
   * @return string
   */
  public static function userModel()
  {
    return static::$userModel;
  }

  /**
   * Get a new instance of the user model.
   *
   * @return mixed
   */
  public static function newUserModel()
  {
    $model = static::userModel();

    return new $model;
  }

  /**
   * Specify the user model that should be used by Serenity.
   *
   * @param  string  $model
   * @return static
   */
  public static function useUserModel(string $model)
  {
    static::$userModel = $model;

    return new static;
  }

  /**
   * Get the name of the team model used by the application.
   *
   * @return string
   */
  public static function teamModel()
  {
    return static::$teamModel;
  }

  /**
   * Get a new instance of the team model.
   *
   * @return mixed
   */
  public static function newTeamModel()
  {
    $model = static::teamModel();

    return new $model;
  }

  /**
   * Specify the team model that should be used by Serenity.
   *
   * @param  string  $model
   * @return static
   */
  public static function useTeamModel(string $model)
  {
    static::$teamModel = $model;

    return new static;
  }

  /**
   * Get the name of the membership model used by the application.
   *
   * @return string
   */
  public static function membershipModel()
  {
    return static::$membershipModel;
  }

  /**
   * Specify the membership model that should be used by Serenity.
   *
   * @param  string  $model
   * @return static
   */
  public static function useMembershipModel(string $model)
  {
    static::$membershipModel = $model;

    return new static;
  }

  /**
   * Get the name of the team invitation model used by the application.
   *
   * @return string
   */
  public static function teamInvitationModel()
  {
    return static::$teamInvitationModel;
  }

  /**
   * Specify the team invitation model that should be used by Serenity.
   *
   * @param  string  $model
   * @return static
   */
  public static function useTeamInvitationModel(string $model)
  {
    static::$teamInvitationModel = $model;

    return new static;
  }

  /**
   * Register a class / callback that should be used to create teams.
   *
   * @param  string  $class
   * @return void
   */
  public static function createTeamsUsing(string $class)
  {
    return app()->singleton(CreatesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to update team names.
   *
   * @param  string  $class
   * @return void
   */
  public static function updateTeamNamesUsing(string $class)
  {
    return app()->singleton(UpdatesTeamNames::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   *
   * @param  string  $class
   * @return void
   */
  public static function addTeamMembersUsing(string $class)
  {
    return app()->singleton(AddsTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   *
   * @param  string  $class
   * @return void
   */
  public static function inviteTeamMembersUsing(string $class)
  {
    return app()->singleton(InvitesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to remove team members.
   *
   * @param  string  $class
   * @return void
   */
  public static function removeTeamMembersUsing(string $class)
  {
    return app()->singleton(RemovesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete teams.
   *
   * @param  string  $class
   * @return void
   */
  public static function deleteTeamsUsing(string $class)
  {
    return app()->singleton(DeletesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete users.
   *
   * @param  string  $class
   * @return void
   */
  public static function deleteUsersUsing(string $class)
  {
    return app()->singleton(DeletesUsers::class, $class);
  }

  /**
   * Manage Serenity's Inertia settings.
   *
   * @return \Serenity\Foundation\InertiaManager
   */
  public static function inertia()
  {
    if (is_null(static::$inertiaManager)) {
      static::$inertiaManager = new InertiaManager;
    }

    return static::$inertiaManager;
  }

  /**
   * Find the path to a localized Markdown resource.
   *
   * @param  string  $name
   * @return string|null
   */
  public static function localizedMarkdownPath($name)
  {
    $localName = preg_replace('#(\.md)$#i', '.'.app()->getLocale().'$1', $name);

    return Arr::first([
      resource_path('markdown/'.$localName),
      resource_path('markdown/'.$name),
    ], function ($path) {
      return file_exists($path);
    });
  }

  /**
   * Get the username used for authentication.
   *
   * @return string
   */
  public static function username()
  {
    return config('serenity.username', 'email');
  }

  /**
   * Get the name of the email address request variable / field.
   *
   * @return string
   */
  public static function email()
  {
    return config('serenity.email', 'email');
  }

  /**
   * Get a completion redirect path for a specific feature.
   *
   * @param  string  $redirect
   * @return string
   */
  public static function redirects(string $redirect, $default = null)
  {
    return config('serenity.redirects.'.$redirect) ?? $default ?? config('serenity.home');
  }

  /**
   * Register the views for Serenity using conventional names under the given namespace.
   *
   * @param  string  $namespace
   * @return void
   */
  public static function viewNamespace(string $namespace)
  {
    static::viewPrefix($namespace.'::');
  }

  /**
   * Register the views for Serenity using conventional names under the given prefix.
   *
   * @param  string  $prefix
   * @return void
   */
  public static function viewPrefix(string $prefix)
  {
    static::loginView($prefix.'login');
    static::twoFactorChallengeView($prefix.'two-factor-challenge');
    static::registerView($prefix.'register');
    static::requestPasswordResetLinkView($prefix.'forgot-password');
    static::resetPasswordView($prefix.'reset-password');
    static::verifyEmailView($prefix.'verify-email');
    static::confirmPasswordView($prefix.'confirm-password');
  }

  /**
   * Specify which view should be used as the login view.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function loginView($view)
  {
    app()->singleton(LoginView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the two factor authentication challenge view.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function twoFactorChallengeView($view)
  {
    app()->singleton(TwoFactorChallengeView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the new password view.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function resetPasswordView($view)
  {
    app()->singleton(ResetPasswordView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the registration view.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function registerView($view)
  {
    app()->singleton(RegisterView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the email verification prompt.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function verifyEmailView($view)
  {
    app()->singleton(VerifyEmailView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the password confirmation prompt.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function confirmPasswordView($view)
  {
    app()->singleton(ConfirmPasswordView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Specify which view should be used as the request password reset link view.
   *
   * @param  callable|string  $view
   * @return void
   */
  public static function requestPasswordResetLinkView($view)
  {
    app()->singleton(RequestPasswordResetLinkView::class, function () use ($view) {
      return new SimpleView($view);
    });
  }

  /**
   * Register a callback that is responsible for building the authentication pipeline array.
   *
   * @param  callable  $callback
   * @return void
   */
  public static function loginThrough(callable $callback)
  {
    static::authenticateThrough($callback);
  }

  /**
   * Register a callback that is responsible for building the authentication pipeline array.
   *
   * @param  callable  $callback
   * @return void
   */
  public static function authenticateThrough(callable $callback)
  {
    static::$authenticateThroughCallback = $callback;
  }

  /**
   * Register a callback that is responsible for validating incoming authentication credentials.
   *
   * @param  callable  $callback
   * @return void
   */
  public static function authenticateUsing(callable $callback)
  {
    static::$authenticateUsingCallback = $callback;
  }

  /**
   * Register a callback that is responsible for confirming existing user passwords as valid.
   *
   * @param  callable  $callback
   * @return void
   */
  public static function confirmPasswordsUsing(callable $callback)
  {
    static::$confirmPasswordsUsingCallback = $callback;
  }

  /**
   * Register a class / callback that should be used to create new users.
   *
   * @param  string  $callback
   * @return void
   */
  public static function createUsersUsing(string $callback)
  {
    app()->singleton(CreatesNewUsers::class, $callback);
  }

  /**
   * Register a class / callback that should be used to update user profile information.
   *
   * @param  string  $callback
   * @return void
   */
  public static function updateUserProfileInformationUsing(string $callback)
  {
    app()->singleton(UpdatesUserProfileInformation::class, $callback);
  }

  /**
   * Register a class / callback that should be used to update user passwords.
   *
   * @param  string  $callback
   * @return void
   */
  public static function updateUserPasswordsUsing(string $callback)
  {
    app()->singleton(UpdatesUserPasswords::class, $callback);
  }

  /**
   * Register a class / callback that should be used to reset user passwords.
   *
   * @param  string  $callback
   * @return void
   */
  public static function resetUserPasswordsUsing(string $callback)
  {
    app()->singleton(ResetsUserPasswords::class, $callback);
  }

  /**
   * Determine if Serenity is confirming two factor authentication configurations.
   *
   * @return bool
   */
  public static function confirmsTwoFactorAuthentication()
  {
    return Features::enabled(Features::twoFactorAuthentication()) &&
           Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
  }

  /**
   * Configure Serenity to not register its routes.
   *
   * @return static
   */
  public static function ignoreRoutes()
  {
    static::$registersRoutes = false;

    return new static;
  }
}
