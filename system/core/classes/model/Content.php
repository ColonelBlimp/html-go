<?php declare(strict_types=1);
namespace html_go\model;

final class Content
{
    const KEY_TITLE = 'title';
    const KEY_DESC = 'description';
    const KEY_BODY = 'body';
    const KEY_LISTING = 'listing';
    const KEY_MENUS = 'menus';
    const KEY_TAGS = 'tags';

    private Site $site;

    /**
     * @var array<string, mixed> $fileData
     */
    private array $fileData;

    /**
     * Content constructor.
     * @param Site $site
     * @param object $dataClass
     */
    function __construct(Site $site, object $dataClass) {
        $this->site = $site;
        $this->fileData = $this->initDataArray();
        $this->fileData = \array_merge($this->fileData, (array)$dataClass);
    }

    /**
     * Initialize the internal data array.
     * @return array<string, mixed>
     */
    private function initDataArray(): array {
        return [
            self::KEY_TITLE => EMPTY_VALUE,
            self::KEY_DESC => EMPTY_VALUE,
            self::KEY_BODY => EMPTY_VALUE,
            self::KEY_LISTING => [],
            self::KEY_MENUS => [],
            self::KEY_TAGS => []
        ];
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
        return $this->fileData[self::KEY_TITLE];
    }

    /**
     * Returns the meta description
     * @return string
     */
    function getDescription(): string {
        return $this->fileData[self::KEY_DESC];
    }

    /**
     * Returns the main raw content body
     * @return string
     */
    function getRawBody(): string {
        return $this->fileData[self::KEY_BODY];
    }

    /**
     * Returns an array of the menus entries for this Content. The key is the menu name and the value
     * is the weight.
     * @return array<string, int>
     */
    function getMenus(): array {
        return $this->fileData[self::KEY_MENUS];
    }

    /**
     * Returns an array of <code>Content</code> object associated with this content.
     * @return array<Content>
     */
    function getListing(): array {
        return $this->fileData[self::KEY_LISTING];
    }

    /**
     * Returns an array of <code>Content</code> tag object associated with this content.
     * @return array<Content>
     */
    function getTags(): array {
        return $this->fileData[self::KEY_TAGS];
    }
}
