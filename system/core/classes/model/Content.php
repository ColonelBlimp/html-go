<?php declare(strict_types=1);
namespace html_go\model;

use html_go\indexing\Element;

final class Content
{
    /**
     * ContentImpl constructor.
     * @param Site $site
     * @param Element $element
     * @param array <string, mixed> $fileData
     */
    function __construct(private Site $site, private Element $element, private array $fileData) {
    }

    /**
     * Returns the site object.
     * @return Site
     */
    function getSite(): Site {
        return $this->site;
    }

    /**
     * Returns the title
     * @return string
     */
    function getTitle(): string {
        return $this->fileData['title'];
    }

    /**
     * Returns the meta description
     * @return string
     */
    function getDescription(): string {
        return $this->fileData['description'];
    }

    /**
     * Returns the main raw content body
     * @return string
     */
    function getRawBody(): string {
        return $this->fileData['body'];
    }

    /**
     * Returns an array of the menus entries for this Content. The key is the menu name and the value
     * is the weight.
     * @return array<string, int>
     */
    function getMenus(): array {
        return $this->fileData['menus'];
    }

    /**
     * Returns an array of <code>Content</code> object associated with this content.
     * @return array<Content>
     */
    function getContentList(): array {
        return $this->fileData['list'];
    }
}
