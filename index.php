<?php declare(strict_types=1);

use html_go\model\Config;

include __DIR__.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

\set_exception_handler('exception_handler');

if (getenv('CLI_ENV', true)) {
//    echo dispatch(HOME_INDEX_KEY.FWD_SLASH.get_config()->getString(Config::KEY_ADMIN_CONTEXT));
//    echo dispatch('admin/category/edit?id=category/uncategorized');
//    echo dispatch('admin');
    echo dispatch('admin/category/add');
} else {
    echo dispatch();
}