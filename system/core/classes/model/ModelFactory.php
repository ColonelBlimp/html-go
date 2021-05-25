<?php declare(strict_types=1);
namespace html_go\model;

use html_go\markdown\MarkdownParser;

/**
 * Responsible for creating <code>Content</code> objects ready to be used in templates.
 * @author Marc L. Veary
 * @since 1.0
 */
final class ModelFactory
{
    private Config $config;
    private MarkdownParser $parser;

    function __construct(Config $config, MarkdownParser $parser) {
        $this->config = $config;
        $this->parser = $parser;
    }

    /**
     * Create a single <code>Content</code> object. Generally, this represents a single piece of
     * content in the system, e.g. post, category, tag, page, etc.
     * @param object $obj Generally, provided by the <code>IndexManager</code>
     * @throws \RuntimeException
     * @return Content
     */
    function createSingleContentObject(object $obj): Content {
        if (!$obj instanceof \stdClass) {
            throw new \RuntimeException("Object parameter not an instance of stdClass: " . print_r($obj, true)); // @codeCoverageIgnore
        }
        return new Content($this->createSiteObject(), $this->loadDataFile($obj));
    }

    /**
     * Create a <code>Content</code> object containing a list of <code>Content</code> objects.
     * Generally, this represents a <i>listing</i> page e.g. latest posts, categories, tags,
     * archive, etc.
     * @param array<Content> $listing the list of Content object to be added
     * @return Content
     */
    function createListContentObject(object $obj, array $listing): Content {
        $obj = $this->loadDataFile($obj);
        $obj->listing = $listing;
        return new Content($this->createSiteObject(), $obj);
    }

    private function createSiteObject(): Site {
        static $site = null;
        if (empty($site)) {
            $site = new Site($this->config);
        }
        return $site;
    }

    /**
     * Creates a custom object whose data is NOT loaded from the filesystem.
     * @param mixed ...$args
     * @return object stdClass
     */
    private function createObject(...$args): object {
        $obj = (object)$args;
        return $obj;
    }

    private function loadDataFile(object $stdClass): object {
        if (!isset($stdClass->path)) {
            throw new \RuntimeException("Object does not have 'path' property " . print_r($stdClass, true)); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($stdClass->path)) === false) {
            throw new \RuntimeException("file_get_contens() failed opening [$stdClass->path]"); // @codeCoverageIgnore
        }
        return $this->parseContentFile($stdClass, $data);
    }

    /**
     * Parse the front matter and added the key/value pairs to the given stdClass, which is
     * then returned.
     * @param object $stdClass
     * @param string $data
     * @throws \RuntimeException
     */
    private function parseContentFile(object $stdClass, string $data): object {
        $str = \str_replace(["\n\r", "\r\n"], "\n", $data);
        $str = \str_replace("\t", " ", $str);
        $lines = \explode("\n", $data);
        $cnt = \count($lines);
        $start = false;
        for ($index = 0; $index < $cnt; $index++) {
            $line = trim($lines[$index]);
            if ($line === '+++') {
                if ($start === false) {
                    $start = true;
                    continue;
                } else {
                    break;
                }
            }

            if (empty($line) || $line[0] === '#') {
                continue;
            }

            if (\strpos($line, '=')) {
                $kv = \explode('=', $line, 2);
                $key = \trim($kv[0], " \n\r\t\v\0\"");
                $val = \trim($kv[1], " \n\r\t\v\0\"");
                $stdClass->$key = $val;
            }
        }

        if (($pos = \strrpos($str, '+++')) === false) {
            throw new \RuntimeException("Somehow, can't find '+++' in [$str]"); // @codeCoverageIgnore
        }

        $stdClass->body = $this->parser->parse(\substr($str, $pos + 3));

        return $stdClass;
    }
}
