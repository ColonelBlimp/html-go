<?php
echo __FILE__;

function admin_route(string $uri, string $method = HTTP_GET): ?\stdClass {
    $content = new stdClass();
    $content->template = '';

    return $content;
}
