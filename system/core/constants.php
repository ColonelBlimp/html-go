<?php declare(strict_types=1);

// Application paths
\define('DS', DIRECTORY_SEPARATOR);
\define('APP_ROOT', \realpath(__DIR__.DS.'..'.DS.'..'));
\define('CONFIG_ROOT', APP_ROOT.DS.'config');
\define('THEMES_ROOT', APP_ROOT.DS.'themes');
\define('CACHE_ROOT', APP_ROOT.DS.'cache');
\define('LANG_ROOT', APP_ROOT.DS.'lang');
\define('AUTHOR_ROOT', CONFIG_ROOT.DS.'users');

// General constants
\define('FWD_SLASH', '/');
\define('CONTENT_FILE_EXT', '.json');
\define('CONTENT_FILE_EXT_LEN', \strlen(CONTENT_FILE_EXT));
\define('MODE', 0777);
\define('DEFAULT_TEMPLATE', 'main.html');
\define('EMPTY_VALUE', '');

// Section constants
\define ('CATEGORY_SECTION', 'category');
\define ('TAG_SECTION', 'tag');
\define ('PAGE_SECTION', 'page');
\define ('POST_SECTION', 'post');

// Landing Page constants for posts and homepage
\define('HOME_INDEX_KEY', '/');
\define('POST_INDEX_KEY', 'blog');
\define('CAT_INDEX_KEY', CATEGORY_SECTION);
\define('TAG_INDEX_KEY', TAG_SECTION);

// dispatcher.php constants
\define('HTTP_GET', 'GET');
\define('HTTP_POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');
\define('POST_REQ_REGEX', '/^\d{4}\/\d{2}\/.+/i');

// Template Variables Keys
\define('TEMPLATE_TPLVAR_KEY', 'template');
