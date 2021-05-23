<?php declare(strict_types=1);

if (!\defined('DS')) {
    \define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__.DS.'..'.DS.'system'.DS.'vendor'.DS.'autoload.php';

\define('TEST_APP_ROOT', __DIR__);
\define('TEST_DATA_ROOT', TEST_APP_ROOT.DS.'test-data');
