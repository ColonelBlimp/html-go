<?php declare(strict_types=1);
namespace html_go\model;

use html_go\traits\ElementCreator;

final class ModelFactory
{
    use ElementCreator;

    function __construct(private Config $config) {
    }

    /**
     * Create a <code>Content</code> object.
     * @param object $obj
     * @param array<mixed> $data
     * @throws \RuntimeException
     * @return Content
     */
    function create(object $obj, array $data = []): Content {
        if (!$obj instanceof \stdClass) {
            throw new \RuntimeException();
        }
        return new Content($this->createSiteObject(), (array)$obj);
    }

    /**
     * Create a <code>Content</code> object for a list page.
     * @param array<Content> $listing
     * @return Content
     */
    function createPostList(array $listing): Content {
        $obj = $this->createElementClass(key: 'posts/index', listing: $listing);
        return new Content($this->createSiteObject(), (array)$obj);
    }

    private function createSiteObject(): Site {
        static $site = null;
        if (empty($site)) {
            $site = new Site($this->config);
        }
        return $site;
    }
}
