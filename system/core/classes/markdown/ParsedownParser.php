<?php declare(strict_types=1);
namespace html_go\markdown;

use Parsedown;

final class ParsedownParser implements Markdown
{
    private Parsedown $parser;

    function __construct() {
        $this->parser = new Parsedown();
    }

    function parse(string $text): string {
        return $this->parser->parse($text);
    }
}
