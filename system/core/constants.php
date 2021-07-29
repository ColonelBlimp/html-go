<?php declare(strict_types=1);

// Application paths
\define('DS', DIRECTORY_SEPARATOR);
\define('APP_ROOT', \realpath(__DIR__.DS.'..'.DS.'..'));
\define('CONFIG_ROOT', APP_ROOT.DS.'config');
\define('THEMES_ROOT', APP_ROOT.DS.'themes');
\define('CACHE_ROOT', APP_ROOT.DS.'cache');
\define('LANG_ROOT', APP_ROOT.DS.'lang');
\define('AUTHOR_ROOT', CONFIG_ROOT.DS.'users');
\define('TEMPLATE_CACHE_ROOT', CACHE_ROOT.DS.'template_cache');

// Admin Console constants
\define('ADMIN_SYS_ROOT', APP_ROOT.DS.'system'.DS.'admin');
\define('ADMIN_THEMES_ROOT', ADMIN_SYS_ROOT.DS.'themes');
\define('ADMIN_ACTION_VIEW', 'view');
\define('ADMIN_ACTION_ADD', 'add');
\define('ADMIN_ACTION_EDIT', 'edit');
\define('ADMIN_ACTION_DELETE', 'delete');
\define('ADMIN_ACTION_CANCEL', 'cancel');

// General constants
\define('FWD_SLASH', '/');
\define('CONTENT_FILE_EXT', '.json');
\define('CONTENT_FILE_EXT_LEN', \strlen(CONTENT_FILE_EXT));
\define('MODE', 0777);
\define('EMPTY_VALUE', '');
\define('NEWLINE_MARKER', '<nl>');
\define('SUMMARY_MARKER', '<!--more-->');
\define('SINGLE_POST_REQUEST', 1);
\define('TIMESTAMP_LEN', 14);

// Section constants
\define('CATEGORY_SECTION', 'category');
\define('TAG_SECTION', 'tag');
\define('PAGE_SECTION', 'page');
\define('POST_SECTION', 'post');

// Landing Page constants for posts and homepage
\define('HOME_INDEX_KEY', '/');
\define('POST_INDEX_KEY', 'blog');
\define('CAT_INDEX_KEY', CATEGORY_SECTION);
\define('TAG_INDEX_KEY', TAG_SECTION);
\define('NOT_FOUND_KEY', 'not-found');

// dispatcher.php constants
\define('HTTP_GET', 'GET');
\define('HTTP_POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');
\define('POST_REQ_REGEX', '/^\d{4}\/\d{2}\/.+/i');

// Template Variables Keys
\define('TEMPLATE_TPLVAR_KEY', 'template');
\define('LIST_TEMPLATE', 'list.html');
\define('SINGLE_TEMPLATE', 'single.html');
\define('DEFAULT_TEMPLATE', SINGLE_TEMPLATE);
