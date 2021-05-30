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

    function createContentObject(Element $indexElement): \stdClass {
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
        if (!isset($contentObject->menus)) {
            $contentObject->menus = [];
        }
        $contentObject->listing = [];
        $contentObject->site = $this->getSiteObject();
        return $contentObject;
    }

    /**
     * Create the site object.
     * @return \stdClass
     */
    private function getSiteObject(): \stdClass {
        static $site = null;
        if (empty($site)) {
            $site = new \stdClass();
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

    private function loadDataFile(Element $indexElement): \stdClass {
        if (!isset($indexElement->path)) {
            throw new \RuntimeException("Object does not have 'path' property " . print_r($indexElement, true)); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($indexElement->path)) === false) {
            throw new \RuntimeException("file_get_contens() failed opening [$indexElement->path]"); // @codeCoverageIgnore
        }
        return $this->parseContentFile($data);
    }

    private function parseContentFile(string $data): \stdClass {
        if (($contentObject = \json_decode($data)) === null) {
            throw new \RuntimeException("json_decode returned null!");
        }
        return $contentObject;
    }
}
