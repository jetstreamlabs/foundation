<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Documentation Routes
    |--------------------------------------------------------------------------
    */

  'docs' => [
    'route' => '/docs',
    'path' => resource_path('markdown/docs'),
    'landing' => 'getting-started',
    'github' => 'jetstreamlabs/serenity', // Github package path
    'twitter' => 'serenityphp', // Twitter username
    'middleware' => ['web'],
  ],

  'styles' => [
    'sidebar' => [
      'headings' => 'font-medium text-gray-900 dark:text-white',
      'links' => 'block w-full pl-3.5 before:pointer-events-none before:absolute before:-left-1 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-y-1/2 before:rounded-full',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Documentation Versions
    |--------------------------------------------------------------------------
    */

  'versions' => [
    'default' => '2.0',
    'published' => [
      '2.0',
      '1.0',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Documentation Settings
    |--------------------------------------------------------------------------
    */

  'settings' => [
    'auth' => false,
    'ga_id' => '',
    'middleware' => [
      'web',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

  'cache' => [
    'enabled' => false,
    'period' => 108000,
  ],

  /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    |
    | Here you can add configure the search functionality of your docs.
    | You can choose the default engine of your search from the list
    | However, you can also enable/disable the search's visibility
    |
    | Supported Search Engines: 'algolia', 'internal'
    |
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

  'seo' => [
    'author' => '',
    'description' => '',
    'keywords' => '',
    'og' => [
      'title' => '',
      'type' => 'article',
      'url' => '',
      'image' => '',
      'description' => '',
    ],
  ],

  'blade-parser' => [
    'regex' => [
      'code-blocks' => [
        'match' => '/\<pre\>(.|\n)*?<\/pre\>/',
        'replacement' => '<code-block>',
      ],
    ],
  ],
];
