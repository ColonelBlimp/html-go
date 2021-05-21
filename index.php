<?php declare(strict_types=1);

include __DIR__.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

if (\getenv('CLI_ENV') !== null) {
    echo dispatch('home');
} else {
    echo dispatch();
}
