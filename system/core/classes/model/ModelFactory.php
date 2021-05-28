<?php declare(strict_types=1);
namespace html_go\model;

use html_go\indexing\Element;
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

    function createContentObject(Element $indexElement): Content {
        $contentObject = $this->loadDataFile($indexElement);
        if (isset($indexElement->key)) {
            $contentObject->key = $indexElement->key;
        }
        if (isset($indexElement->category)) {
            $contentObject->category = $indexElement->category;
        }
        if (isset($indexElement->tags)) {
            $contentObject->tags = $indexElement->tags;
        }
        $contentObject->menus = [];
        $contentObject->listing = [];
        $contentObject->site = $this->getSiteObject();
        return $contentObject;
    }

    /**
     * Create the site object.
     * @return Site
     */
    private function getSiteObject(): Site {
        static $site = null;
        if (empty($site)) {
            $site = new Site();
            $site->url = $this->config->getString(Config::KEY_SITE_URL);
            $site->name = $this->config->getString(Config::KEY_SITE_NAME);
            $site->title = $this->config->getString(Config::KEY_SITE_TITLE);
            $site->description = $this->config->getString(Config::KEY_SITE_DESCRIPTION);
            $site->tagline = $this->config->getString(Config::KEY_SITE_TAGLINE);
            $site->copyright = $this->config->getString(Config::KEY_SITE_COPYRIGHT);
            $site->language = $this->config->getString(Config::KEY_LANG);
        }
        return $site;
    }

    private function loadDataFile(Element $indexElement): Content {
        if (!isset($indexElement->path)) {
            throw new \RuntimeException("Object does not have 'path' property " . print_r($indexElement, true)); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($indexElement->path)) === false) {
            throw new \RuntimeException("file_get_contens() failed opening [$indexElement->path]"); // @codeCoverageIgnore
        }
        return $this->parseContentFile($data);
    }

    private function parseContentFile(string $data): Content {
        $str = \str_replace(["\n\r", "\r\n"], "\n", $data);
        $str = \str_replace("\t", " ", $str);
        $lines = \explode("\n", $data);
        $cnt = \count($lines);
        $start = false;
        $contentObject = new Content();
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
                if (isset($contentObject->$key)) {
                    throw new \RuntimeException("Overwriting an existing key/value [$key]"); // @codeCoverageIgnore
                }
                $contentObject->$key = $val;
            }
        }

        if (($pos = \strrpos($str, '+++')) === false) {
            throw new \RuntimeException("Somehow, can't find '+++' in [$str]"); // @codeCoverageIgnore
        }

        $contentObject->body = $this->parser->parse(\substr($str, $pos + 3));

        return $contentObject;
    }
}
