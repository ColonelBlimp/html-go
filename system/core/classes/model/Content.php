<?php declare(strict_types=1);

namespace html_go\model;

final class Content implements ContentInterface
{
    function __construct() {
    }
    function getTitle(): string {
        return __FUNCTION__;
    }
    function getBody(): string {
        return __FUNCTION__;
    }
    function getDescription(): string {
        return __FUNCTION__;
    }
}
