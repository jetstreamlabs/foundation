<?php

use Serenity\Features;

return [
  'allowed' => [
    'actions' => [
      '__construct',
      '__invoke',
    ],
    'responders' => [
      'send', 'make',
    ],
  ],
  'pages' => [
    'path' => env('PAGE_PATH', 'resources/content/pages'),
  ],
  'collections' => [
    'path' => env('COLLECTION_PATH', 'resources/content/collections'),
  ],

  'stack' => 'inertia',
  'guard' => 'sanctum',
  'auth_guard' => 'web',
  'passwords' => 'users',
  'username' => 'email',
  'email' => 'email',
  'home' => '/dashboard',
  'prefix' => '',
  'domain' => null,
  'middleware' => ['web'],
  'auth_middleware' => 'auth',
  'limiters' => [
    'login' => 'login',
    'two-factor' => 'two-factor',
  ],
  'views' => true,
  'features' => [
    Features::termsAndPrivacyPolicy(),
    Features::profilePhotos(),
    Features::api(),
    Features::teams(['invitations' => true]),
    Features::accountDeletion(),
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
      'confirm' => true,
      'confirmPassword' => true,
      // 'window' => 0,
    ]),
  ],
  'profile_photo_disk' => 'public',
  'redirects' => [
    'login' => null,
    'logout' => null,
    'password-confirmation' => null,
    'register' => null,
    'email-verification' => null,
    'password-reset' => null,
  ],
  'action_directory' => [
    // app_path('Actions'),
  ],
  'docs_directory' => [
    // 'docs' => resource_path('views/docs'),
  ],
  'pending_route_transformers' => [
    ...Serenity\Routing\Transformers::defaultRouteTransformers(),
  ],
];
