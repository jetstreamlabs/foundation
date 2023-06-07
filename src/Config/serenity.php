<?php

use App\Domain\Providers\RouteServiceProvider;
use Serenity\Foundation\Features;
use Serenity\Middleware\AuthenticateSession;

return [

  /**
   * ALLOWED ACTIONS
   *
   * These are the only methods allowed in Actions and Responders.
   * It's not recommended to create actions with methods other
   * than the default. Generators will not create resource actions.
   */
  'allowed' => [
    'actions' => [
      '__construct',
      '__invoke',
    ],
    'responders' => [
      'send',
      'make',
      'replace',
      'toResponse',
    ],
  ],

  /**
   * SERENITY STACK
   *
   * This configuration value informs Serenity which "stack" you will be
   * using for your application. In general, this value is set for you
   * during installation and will not need to be changed after that.
   */
  'stack' => 'inertia',

  /**
   * SERENITY GUARDS
   *
   * Here you may specify which authentication guards Serenity will use while
   * authenticating users. This value should correspond with one of your
   * guards that is already present in your "auth" configuration file.
   */
  'guard' => 'sanctum',
  'auth_guard' => 'web',

  /**
   * PASSWORD BROKER
   *
   * Here you may specify which password broker Serenity can use when a user
   * is resetting their password. This configured value should match one
   * of your password brokers setup in your "auth" configuration file.
   */
  'passwords' => 'users',

  /**
   * USERNAME / EMAIL
   *
   * This value defines which model attribute should be considered as your
   * application's "username" field. Typically, this might be the email
   * address of the users but you are free to change this value here.
   *
   * Out of the box, Serenity expects forgot password and reset password
   * requests to have a field named 'email'. If the application uses
   * another name for the field you may define it below as needed.
   */
  'username' => 'email',
  'email' => 'email',

  /**
   * SALUTATIONS
   *
   * These values dictate the hello and goodbye messages for your
   * Serenity application. At the point the messages are generated, the
   * user's first name is available so it's encouraged to pass it in
   * your messages either in the title or message.
   *
   * Styles available: success, info, error, status, warning
   *
   * If you don't want one or both of the messages simple set them to
   * null like so:
   *
   * 'hello' => null,
   * 'goodbye' => null,
   */
  'hello' => [
    'title' => 'Hey %name%',
    'message' => "Welcome back, it's good to see you.",
    'style' => 'success',
  ],
  'goodbye' => [
    'title' => 'Later %name%',
    'message' => 'We hope to see you back again soon.',
    'style' => 'info',
  ],

  /**
   * HOME PATH
   *
   * Here you may configure the path where users will get redirected during
   * authentication or password reset when the operations are successful
   * and the user is authenticated. You are free to change this value.
   */
  'home' => RouteServiceProvider::HOME,

  /**
   * ROUTES PREFIX / SUBDOMAIN
   *
   * Here you may specify which prefix Serenity will assign to all the routes
   * that it registers with the application. If necessary, you may change
   * subdomain under which all of the Serenity routes will be available.
   */
  'prefix' => '',
  'domain' => null,

  /**
   * ROUTES MIDDLEWARE
   *
   * Here you may specify which middleware Serenity will assign to the routes
   * that it registers with the application. If necessary, you may change
   * these middleware but typically this provided default is preferred.
   */
  'middleware' => ['web'],
  'auth_middleware' => AuthenticateSession::class,

  /**
   * RATE LIMITING
   *
   * By default, Serenity will throttle logins to five requests per minute for
   * every email and IP address combination. However, if you would like to
   * specify a custom rate limiter to call then you may specify it here.
   */
  'limiters' => [
    'login' => 'login',
    'two-factor' => 'two-factor',
  ],

  /**
   * REGISTER ROUTE VIEWS
   *
   * Here you may specify if the routes returning views should be disabled as
   * you may not need them when building your own application. This may be
   * especially true if you're writing a custom single-page application.
   * Serenity DOES NOT use route views of any kind.
   */
  'views' => false,

  /**
   * FEATURES
   *
   * Some of the Serenity features are optional. You may disable the features
   * by removing them from this array. You're free to only remove some of
   * these features or you can even remove all of these if you need to.
   */
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

  /**
   * PROFILE IMAGE DISK & VISIBILITY
   *
   * Location and visibility of your profile images.
   */
  'profile_photo_disk' => 'public',

  /**
   * DEFAULT REDIRECTS
   *
   * Default redirect routes for the various authentication actions.
   * Unless you have a specific need, you should leave these as is.
   */
  'redirects' => [
    'login' => null,
    'logout' => null,
    'password-confirmation' => null,
    'register' => null,
    'email-verification' => null,
    'password-reset' => null,
  ],

  /**
   * REGISTER ROUTES BY CONFIG
   *
   * Routes will be registered for all actions found in these directories.
   * If you have middleware protected routes, you should use the finder
   * method in your routes files to guard against unwanted intrusion.
   */
  'action_directory' => [
    // app_path('Actions'),
  ],

  /**
   * ROUTE TRANSFORMERS
   *
   * After having discovered all actions, these classes will manipulate the routes
   * before registering them to Serenity.
   *
   * In most cases, you shouldn't change these.
   */
  'pending_route_transformers' => [
    ...Serenity\Routing\Transformers::defaultRouteTransformers(),
  ],

  /**
   * CONTRACT TO CONCRETE AUTO MAPPING
   *
   * Interfaces are automatically mapped for the below implementations
   * in the \Domain\Providers\ContractMappingServiceProvider::class.
   *
   * Add your own mapping in the provider, or set these to false if
   * you prefer to register and map them manually.
   */
  'mapping' => [
    'responders' => true,
    'repositories' => true,
  ],

  /**
   * DOCUMENTATION ROUTES
   *
   * This option provides an array of settings required by the Serenity
   * documentation package. Modify them to fit your project documentation.
   */
  'docs' => [
    'route' => '/docs',
    'path' => resource_path('markdown/docs'),
    'landing' => 'getting-started',
    'github' => 'jetstreamlabs/serenity', // Github package path
    'twitter' => 'serenityphp', // Twitter username
    'middleware' => ['web'],
  ],

  /**
   * DOCUMENTATION VERSIONS
   *
   * Provide the published and default versions of your docs for proper
   * routing and UI implementation.
   */
  'versions' => [
    'default' => '1.0',
    'published' => [
      '1.0',
    ],
  ],

  /**
   * DOCUMENTATION CACHE SETTINGS
   *
   * Turn the cache on or off, and set the cache time (minutes) for docs.
   */
  'cache' => [
    'enabled' => false,
    'period' => 20,
  ],

  /**
   * DOCUMENTATION SEARCH
   *
   * Here you can add configure the search functionality of your docs.
   * You can choose the default engine of your search from the list
   * However, you can also enable/disable the search's visibility
   *
   * Supported Search Engines: 'algolia', 'internal'
   */
  'search' => [
    'enabled' => false,
    'default' => 'algolia',
    'engines' => [
      'internal' => [
        'index' => ['h2', 'h3'],
      ],
      'algolia' => [
        'key' => '',
        'index' => '',
      ],
    ],
  ],

  /**
   * OPENAI API KEY AND ORGANIZATION
   *
   * Here you may specify your OpenAI API Key and organization. This will be
   * used to authenticate with the OpenAI API - you can find your API key
   * and organization on your OpenAI dashboard, at https://openai.com.
   */
  'api_key' => env('OPENAI_API_KEY'),
  'organization' => env('OPENAI_ORGANIZATION'),
];
