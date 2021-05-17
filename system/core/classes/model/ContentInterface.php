<?php declare(strict_types=1);
namespace html_go\model;

interface ContentInterface
{
    function getTitle(): string;
    function getDescription(): string;
    function getBody(): string;
}
