<?php declare(strict_types=1);

use html_go\model\Config;

include __DIR__.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
if (getenv('CLI_ENV', true)) {
//    echo dispatch(HOME_INDEX_KEY.FWD_SLASH.get_config()->getString(Config::KEY_ADMIN_CONTEXT));
    echo dispatch('admin/category/edit?id=1');
} else {
    echo dispatch();
}