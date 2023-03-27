<?php

/*
 * This file came from Laravel Markdown.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

  /*
   |--------------------------------------------------------------------------
   | CommonMark Extensions
   |--------------------------------------------------------------------------
   |
   | This option specifies what extensions will be automatically enabled.
   | Simply provide your extension class names here.
   |
   */

  'extensions' => [],

  /*
   |--------------------------------------------------------------------------
   | Renderer Configuration
   |--------------------------------------------------------------------------
   |
   | This option specifies an array of options for rendering HTML.
   |
   */

  'renderer' => [
    'block_separator' => "\n",
    'inner_separator' => "\n",
    'soft_break' => "\n",
  ],

  /*
   |--------------------------------------------------------------------------
   | Commonmark Configuration
   |--------------------------------------------------------------------------
   |
   | This option specifies an array of options for commonmark.
   |
   */

  'commonmark' => [
    'enable_em' => true,
    'enable_strong' => true,
    'use_asterisk' => true,
    'use_underscore' => true,
    'unordered_list_markers' => ['-', '+', '*'],
  ],

  'table_of_contents' => [
    'html_class' => 'table-of-contents',
    'position' => 'placeholder',
    'style' => 'bullet',
    'min_heading_level' => 2,
    'max_heading_level' => 3,
    'normalize' => 'relative',
    'placeholder' => '[[toc]]',
  ],

  'heading_permalink' => [
    'html_class' => 'heading-permalink',
    'id_prefix' => 'content',
    'fragment_prefix' => 'content',
    'insert' => 'before',
    'min_heading_level' => 2,
    'max_heading_level' => 3,
    'title' => 'Permalink',
    'symbol' => '',
    'aria_hidden' => true,
  ],

  /*
   |--------------------------------------------------------------------------
   | HTML Input
   |--------------------------------------------------------------------------
   |
   | This option specifies how to handle untrusted HTML input.
   |
   */

  'html_input' => 'strip',

  /*
   |--------------------------------------------------------------------------
   | Allow Unsafe Links
   |--------------------------------------------------------------------------
   |
   | This option specifies whether to allow risky image URLs and links.
   |
   */

  'allow_unsafe_links' => true,

  /*
   |--------------------------------------------------------------------------
   | Maximum Nesting Level
   |--------------------------------------------------------------------------
   |
   | This option specifies the maximum permitted block nesting level.
   |
   */

  'max_nesting_level' => PHP_INT_MAX,

  /*
   |--------------------------------------------------------------------------
   | Slug Normalizer
   |--------------------------------------------------------------------------
   |
   | This option specifies an array of options for slug normalization.
   |
   */

  'slug_normalizer' => [
    'max_length' => 255,
    'unique' => 'document',
  ],

];
