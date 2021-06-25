<?php declare(strict_types=1);
namespace html_go\model;

use DateTimeInterface;
use InvalidArgumentException;
use html_go\exceptions\InternalException;
use html_go\indexing\IndexManager;
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
    private IndexManager $manager;

    /**
     * ModelFactory constructor.
     * @param Config $config
     * @param MarkdownParser $parser Implementation of the
     * <code>MarkdownParser</code> interface.
     */
    public function __construct(Config $config, MarkdownParser $parser, IndexManager $manager) {
        $this->config = $config;
        $this->parser = $parser;
        $this->manager = $manager;
    }

    /**
     * Create a content object (stdClass) from an index object (stdClass).
     * @param \stdClass $indexElement As obtained from the <code>IndexManager</code>
     * @return \stdClass
     */
    public function createContentObject(\stdClass $indexElement): \stdClass {
        $contentObject = $this->getContentObject($indexElement);
        $contentObject->key = $indexElement->key;
        $contentObject->list = [];
        $contentObject->site = $this->getSiteObject();

        if (!empty($indexElement->category)) {
            $contentObject->category = $this->getCategoryObject($indexElement->category);
        }
        if (isset($indexElement->tags)) {
            $contentObject->tags = $indexElement->tags;
        }
        $dt = $this->getContentDateAndTimestamp($indexElement);
        $contentObject->date = $dt[0];
        $contentObject->timestamp = $dt[1];

        if (empty($contentObject->summary)) {
            $contentObject->summary = $this->getSummary($contentObject->body);
        }

        return $contentObject;
    }

    private function getContentObject(\stdClass $indexElement): \stdClass {
        if ($indexElement->section === TAG_SECTION) {
            $contentObject = $this->createEmptyContentObject();
            $contentObject->title = \substr($indexElement->key, \strlen(TAG_SECTION) + 1);
        } else {
            $contentObject = $this->loadDataFile($indexElement);
            $contentObject->body = $this->restoreNewlines($contentObject->body);
        }
        $contentObject->template = EMPTY_VALUE;
        return $contentObject;
    }

    /**
     * @param \stdClass $indexElement
     * @return array<string>
     */
    private function getContentDateAndTimestamp(\stdClass $indexElement): array {
        if (empty($indexElement->timestamp) === false) {
            $dt = new \DateTime($indexElement->timestamp);
            $date = $dt->format($this->config->getString(Config::KEY_POST_DATE_FMT));
            $timestamp = $dt->format(DateTimeInterface::W3C);
        } else {
            $date = $timestamp = EMPTY_VALUE;
        }
        return [$date, $timestamp];
    }

    private function getCategoryObject(string $slug): \stdClass {
        if ($this->manager->elementExists($slug) === false) {
            throw new \UnexpectedValueException("Element does not exist [$slug]"); // @codeCoverageIgnore
        }
        return $this->loadDataFile($this->manager->getElementFromSlugIndex($slug));
    }

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
        if (empty($indexElement->path)) {
            throw new InvalidArgumentException("Object does not have 'path' property "./** @scrutinizer ignore-type */print_r($indexElement, true)); // @codeCoverageIgnore
        }
        $path = $indexElement->path;
        if (($data = \file_get_contents($path)) === false) {
            throw new InternalException("file_get_contents() failed opening [$path]"); // @codeCoverageIgnore
        }
        if (($contentObject = \json_decode($data)) === null) {
            throw new InternalException("json_decode returned null decoding [$data] from [$path]"); // @codeCoverageIgnore
        }
        return $contentObject;
    }

    /**
     * This is used for tags only as tags don't have an associated file on the
     * filesystem.
     * @return \stdClass
     */
    private function createEmptyContentObject(): \stdClass {
        $obj = new \stdClass();
        $obj->key = EMPTY_VALUE;
        $obj->section = EMPTY_VALUE;
        $obj->body = EMPTY_VALUE;
        $obj->title = EMPTY_VALUE;
        $obj->description = EMPTY_VALUE;
        return $obj;
    }

    /**
     * Returns the summary for the content object. If '<!--more-->' is used within
     * the body, then this is removed once the summary is obtained.
     * @param string $body the body. The '<!--more--> removed if found thus passed by reference.
     * @return string The summary text. If no summary is defined, returns an empty string
     */
    private function getSummary(string &$body): string {
        $pos = \strpos($body, SUMMARY_MARKER);
        if ($pos !== false) {
            $summary = \substr($body, 0, $pos);
            $body = \str_replace(SUMMARY_MARKER, '', $body);
            return $summary;
        }
        return '';
    }

    /**
     * Newlines must be encoded for PHP functions. So we use '<nl>' to for '\n'.
     * This method replaces '<nl>' with '\n'.
     * @param string $text
     * @return string
     */
    private function restoreNewlines(string $text): string {
        return \str_replace(NEWLINE_MARKER, '\n', $text);
    }
}
