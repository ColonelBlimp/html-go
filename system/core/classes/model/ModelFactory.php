<?php declare(strict_types=1);
namespace html_go\model;

/**
 * Responsible for creating <code>Content</code> objects ready to be used in templates.
 * @author Marc L. Veary
 * @since 1.0
 */
final class ModelFactory
{
    function __construct(private Config $config) {
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
            throw new \RuntimeException();
        }
        return new Content($this->createSiteObject(), $obj);
    }

    /**
     * Create a <code>Content</code> object containing a list of <code>Content</code> objects.
     * Generally, this represents a <i>listing</i> page e.g. latest posts, categories, tags,
     * archive, etc.
     * @param array<Content> $listing the list of Content object to be added
     * @return Content
     */
    function createListContentObject(string $title, array $listing): Content {
        return new Content($this->createSiteObject(), $this->createObject(title: $title, listing: $listing));
    }

    private function createSiteObject(): Site {
        static $site = null;
        if (empty($site)) {
            $site = new Site($this->config);
        }
        return $site;
    }

    /**
     * @param mixed ...$args
     * @return object stdClass
     */
    private function createObject(...$args): object {
        $obj = (object)$args;
        print_r($obj);
        return $obj;
    }
}
