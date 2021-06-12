<?php declare(strict_types=1);
namespace html_go\markdown;

/**
 * Implement this interface to for which ever markdown parser wanted.
 * @author Marc L. Veary
 * @since 1.0
 */
interface MarkdownParser
{
    /**
     * Parse the give markdown text.
     * @param string $text
     * @return string
     */
    public function parse(string $text): string;
}
