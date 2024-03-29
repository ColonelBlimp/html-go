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
\define('CONTENT_ROOT', APP_ROOT.DS.'content');
\define('CATEGORY_ROOT', CONTENT_ROOT.DS.'common'.DS.'category');

// Admin Console constants
\define('ADMIN_SYS_ROOT', APP_ROOT.DS.'system'.DS.'admin');
\define('ADMIN_THEMES_ROOT', ADMIN_SYS_ROOT.DS.'themes');
\define('ADMIN_ACTION_VIEW', 'view');
\define('ADMIN_ACTION_ADD', 'add');
\define('ADMIN_ACTION_EDIT', 'edit');
\define('ADMIN_ACTION_DELETE', 'delete');
\define('ADMIN_ACTION_CANCEL', 'cancel');
\define('ADMIN_CONTEXT_STR', 'context');
\define('ADMIN_ACTION_STR', 'action');
\define('ADMIN_KEY_STR', 'key');

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
\define('ID_STR', 'id');

// Section constants
\define('CATEGORY_SECTION', 'category');
\define('TAG_SECTION', 'tag');
\define('PAGE_SECTION', 'page');
\define('POST_SECTION', 'post');
\define('ADMIN_CONSOLE_SECTION', 'admin');

// Landing Page constants for posts and homepage
\define('HOME_INDEX_KEY', '/');
\define('POST_INDEX_KEY', 'blog');
\define('CAT_INDEX_KEY', CATEGORY_SECTION);
\define('TAG_INDEX_KEY', TAG_SECTION);
\define('NOT_FOUND_KEY', 'not-found');
\define('ADMIN_DASHBOARD_KEY', HOME_INDEX_KEY);

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
