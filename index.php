<?php declare(strict_types=1);

use html_go\indexing\IndexManager;
include __DIR__.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
if (getenv('CLI_ENV', true)) {
    echo dispatch(HOME_INDEX_KEY);
} else {
    echo dispatch();
}