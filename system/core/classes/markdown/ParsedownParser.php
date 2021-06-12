<?php declare(strict_types=1);
namespace html_go\markdown;

use Parsedown;

/**
 * Implementation for <code>Parsedown</code>.
 * @author Marc L. Veary
 * @since 1.0
 */
final class ParsedownParser implements MarkdownParser
{
    private Parsedown $parser;

    public function __construct() {
        $this->parser = new Parsedown();
    }

    public function parse(string $text): string {
        return $this->parser->parse($text);
    }
}
