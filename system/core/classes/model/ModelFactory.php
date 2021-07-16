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
final class ModelFactory extends AdminModelFactory
{
    private MarkdownParser $parser;
    private IndexManager $manager;

    /**
     * ModelFactory constructor.
     * @param Config $config
     * @param MarkdownParser $parser Implementation of the
     * <code>MarkdownParser</code> interface.
     */
    public function __construct(Config $config, MarkdownParser $parser, IndexManager $manager) {
        parent::__construct($config);
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
            $contentObject = $this->createEmptyContentObject($indexElement);
            $contentObject->title = \substr($indexElement->key, \strlen(TAG_SECTION) + 1);
        } else {
            $contentObject = $this->loadDataFile($indexElement);
            $contentObject->body = $this->restoreNewlines($contentObject->body);
        }
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

    /**
     * This loads the content's associated file and merges it with the index element.
     * @param \stdClass $indexElement
     * @throws InvalidArgumentException
     * @throws InternalException
     * @return \stdClass
     */
    private function loadDataFile(\stdClass $indexElement): \stdClass {
        if (empty($indexElement->path)) {
            throw new InvalidArgumentException("Object does not have 'path' property "./** @scrutinizer ignore-type */print_r($indexElement, true)); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($indexElement->path)) === false) {
            throw new InternalException("file_get_contents() failed opening [$indexElement->path]"); // @codeCoverageIgnore
        }
        if (($fileData = \json_decode($data, true)) === null) {
            throw new InternalException("json_decode returned null decoding [$fileData] from [$indexElement->path]"); // @codeCoverageIgnore
        }
        return (object)\array_merge((array)$indexElement, $fileData);
    }

    /**
     * This is used for tags only as tags don't have an associated file on the
     * filesystem.
     * @param \stdClass $indexElement
     * @return \stdClass
     */
    private function createEmptyContentObject(\stdClass $indexElement): \stdClass {
        $obj = new \stdClass();
        $obj->key = EMPTY_VALUE;
        $obj->section = TAG_SECTION;
        $obj->body = EMPTY_VALUE;
        $obj->title = EMPTY_VALUE;
        $obj->description = EMPTY_VALUE;
        $obj->timestamp = EMPTY_VALUE;
        return (object)\array_merge((array)$indexElement, (array)$obj);
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
