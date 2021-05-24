<?php declare(strict_types=1);
namespace html_go\markdown;

interface Markdown
{
    function parse(string $text): string;
}
