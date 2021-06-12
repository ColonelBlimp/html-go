<?php declare(strict_types=1);
namespace html_go\model;

use DateTimeInterface;
use InvalidArgumentException;
use html_go\exceptions\InternalException;
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

    /**
     * ModelFactory constructor.
     * @param Config $config
     * @param MarkdownParser $parser Implementation of the
     * <code>MarkdownParser</code> interface.
     */
    public function __construct(Config $config, MarkdownParser $parser) {
        $this->config = $config;
        $this->parser = $parser;
    }

    /**
     * Create a content object (stdClass) from an index <code>Element</code>
     * object.
     * @param \stdClass $indexElement As obtained from the <code>IndexManager</code>
     * @return \stdClass
     */
    public function createContentObject(\stdClass $indexElement): \stdClass {
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
        if (isset($indexElement->date)) {
            if (EMPTY_VALUE === $indexElement->date) {
                $contentObject->date = $indexElement->date;
                $contentObject->timestamp = EMPTY_VALUE;
            } else {
                $dt = new \DateTime($indexElement->date);
                $contentObject->date = $dt->format($this->config->getString(Config::KEY_POST_DATE_FMT));
                $contentObject->timestamp = $dt->format(DateTimeInterface::W3C);
            }
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
            $site->theme = $this->config->getString(Config::KEY_THEME_NAME);
            $site->tpl_engine = $this->config->getString(Config::KEY_TPL_ENGINE);
        }
        return $site;
    }

    private function loadDataFile(\stdClass $indexElement): \stdClass {
        if (!isset($indexElement->path) || empty($indexElement->path)) {
            throw new InvalidArgumentException("Object does not have 'path' property "./** @scrutinizer ignore-type */print_r($indexElement, true)); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($indexElement->path)) === false) {
            throw new InternalException("file_get_contents() failed opening [$indexElement->path]"); // @codeCoverageIgnore
        }
        if (($contentObject = \json_decode($data)) === null) {
            $path = $indexElement->path;
            throw new InternalException("json_decode returned null decoding [$data] from [$path]"); // @codeCoverageIgnore
        }
        return $contentObject;
    }
}
