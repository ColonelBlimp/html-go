<?php declare(strict_types=1);
namespace html_go\model;

final class Content
{
    private Site $site;

    /**
     * @var array<string, mixed> $fileData
     */
    private array $fileData;

    /**
     * Content constructor.
     * @param Site $site
     * @param array <string, mixed> $fileData
     */
    function __construct(Site $site, array $fileData) {
        $this->site = $site;
        $this->fileData = [
            'key' => '',
            'description' => '',
            'body' => '',
            'menus' => [],
            'list' => [],
            'tags' => []
        ];
        $this->fileData = \array_merge($this->fileData, $fileData);
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
        return $this->fileData['key'];
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

    /**
     * Returns an array of <code>Content</code> tag object associated with this content.
     * @return array<Content>
     */
    function getTags(): array {
        return $this->fileData['tags'];
    }
}
