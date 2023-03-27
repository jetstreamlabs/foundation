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
    'period' => 5,
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

  /*
    |--------------------------------------------------------------------------
    | Appearance
    |--------------------------------------------------------------------------
    |
    | Here you can add configure the appearance of your docs. For example,
    | you can set the primary and secondary colors that will give your
    | documentation a unique look. You can set the fav of your docs.
    |
    |
    */

  'ui' => [
    'code_theme' => 'dark', // or: light
    'fav' => '',     // eg: fav.png
    'fa_v4_shims' => true, // Add FontAwesome v4 shims prevent BC break
    'show_side_bar' => true,
    'colors' => [
      'primary' => '#787AF6',
      'secondary' => '#2b9cf2',
    ],

    'theme_order' => null, // ['RecipeDarkTheme', 'customTheme']
  ],

  /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    |
    | These options configure the SEO settings of your docs. You can set the
    | author, the description and the keywords. Also, Recipe by default
    | sets the canonical link to the viewed page's link automatically.
    |
    |
    */

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

  /*
   |--------------------------------------------------------------------------
   | Forum
   |--------------------------------------------------------------------------
   |
   | Giving a chance to your users to post their questions or feedback
   | directly on your docs, is pretty nice way to engage them more.
   | However, you can also enable/disable the forum's visibility.
   |
   | Supported Services: 'disqus'
   |
   */

  'forum' => [
    'enabled' => false,
    'default' => 'disqus',
    'services' => [
      'disqus' => [
        'site_name' => '', // yoursite.disqus.com
      ],
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Components and Packages
    |--------------------------------------------------------------------------
    |
    | Once you create a new asset or theme, its directory will be
    | published under `Recipe-components` folder. However, If
    | you want a different location, feel free to change it.
    |
    |
    */

  'packages' => [
    'path' => 'Recipe-components',
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
