<?php declare(strict_types=1);
namespace html_go\indexing;


if (!\defined('MODE')) {
    \define('MODE', 0777);
}

//TODO: Replace this with ENUMS from php 8.1
\define('ENUM_PAGE', 'pages');
\define('ENUM_CATEGORY', 'categories');
\define('ENUM_POST', 'posts');
\define('ENUM_TAG', 'tags');

final class IndexManager
{
    private const CATEGORIES_DIR = 'content'.DS.'common'.DS.'category';
    private const PAGES_DIR = 'content'.DS.'common'.DS.'pages';
    private const INDEX_DIR = 'cache'.DS.'indexes';
    private const USER_DATA_DIR = 'content'.DS.'user-data';

    private const PAGE_INDEX_FILE = self::INDEX_DIR.DS.'pages.inx';
    private const POST_INDEX_FILE = self::INDEX_DIR.DS.'posts.inx';
    private const CAT_INDEX_FILE = self::INDEX_DIR.DS.'categories.inx';
    private const TAG_INDEX_FILE = self::INDEX_DIR.DS.'tags.inx';
    private const TAG2POSTS_INDEX_FILE = self::INDEX_DIR.DS.'tag2posts.inx';
    private const CAT2POSTS_INDEX_FILE = self::INDEX_DIR.DS.'cat2posts.inx';
    private const MENUS_INDEX_FILE = self::INDEX_DIR.DS.'menus.inx';

    private const POST_LANDING_FILE = 'content'.DS.'common'.DS.'landing'.DS.'posts'.DS.'index'.CONTENT_FILE_EXT;
    private const TAG_LANDING_FILE = 'content'.DS.'common'.DS.'landing'.DS.'tags'.DS.'index'.CONTENT_FILE_EXT;
    private const CAT_LANDING_FILE = 'content'.DS.'common'.DS.'landing'.DS.'category'.DS.'index'.CONTENT_FILE_EXT;

    private string $root;
    private string $categoriesDir;
    private string $pagesDir;
    private string $userDataDir;
    private string $indexDir;

    /**
     * @var array<string, Element> $slugIndex
     */
    private array $slugIndex;

    /**
     * @var array<string, array<int, string>> $category2PostsIndex
     */
    private array $category2PostsIndex;

    /**
     * @var array<string, array<int, string>> $tag2PostIndex
     */
    private array $tag2PostIndex;

    /**
     * @var array<string, Element> $postIndex
     */
    private array $postIndex;

    /**
     * @var array<string, Element> $pageIndex
     */
    private array $pageIndex;

    /**
     * @var array<string, Element> $categoryIndex
     */
    private array $categoryIndex;

    /**
     * @var array<string, Element> $tagIndex
     */
    private array $tagIndex;

    /**
     * @var array<mixed> $menusIndex
     */
    private array $menusIndex;

    /**
     * IndexManager constructor.
     * @param string $root
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    function __construct(string $root) {
        if (($tmp = \realpath($root)) === false) {
            throw new \InvalidArgumentException("Unable to validate the location of the 'content' directory [$root]");
        }
        $this->root = $tmp;
        $this->categoriesDir = $this->root.DS.self::CATEGORIES_DIR;
        $this->pagesDir = $this->root.DS.self::PAGES_DIR;
        $this->userDataDir = $this->root.DS.self::USER_DATA_DIR;
        $this->indexDir = $this->root.DS.self::INDEX_DIR;

        if (!\is_dir($this->categoriesDir)) {
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$this->categoriesDir]"); // @codeCoverageIgnore
        }
        if (!\is_dir($this->pagesDir)) {
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$this->pagesDir]"); // @codeCoverageIgnore
        }
        if (!\is_dir($this->userDataDir)) {
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$this->userDataDir]"); // @codeCoverageIgnore
        }
        if (!\file_exists($this->indexDir)) {
            if (\mkdir($this->indexDir, MODE, true) === false) {
                throw new \RuntimeException("Unable to create directory [$this->indexDir]"); // @codeCoverageIgnore
            }
        }

        $this->Initialize();
    }

    function reindex(): void {
        $this->categoryIndex = $this->buildCategoryIndex();
        $this->pageIndex = $this->buildPageIndex();
        $this->postIndex = $this->buildPostsIndex();
        $this->tagIndex = $this->buildTagIndex($this->tag2PostIndex, $this->category2PostsIndex);
    }

    /**
     * Returns an object representing an element in the index.
     * @param string $key
     * @throws \RuntimeException
     * @return Element
     */
    function getElementFromSlugIndex(string $key): Element {
        if (!isset($this->slugIndex[$key])) {
            throw new \RuntimeException("Key does not exist in the slugIndex! Use 'elementExists()' before calling this method.");
        }
        return $this->slugIndex[$key];
    }

    /**
     * Check if an key exists in the <b>slug index</b>.
     * @param string $key
     * @return bool <code>true</code> if exists, otherwise <code>false</code>
     */
    function elementExists(string $key): bool {
        return isset($this->slugIndex[$key]);
    }

    /**
     * Return the posts index.
     * @return array<string, Element>
     */
    function getPostsIndex(): array {
        return $this->postIndex;
    }

    /**
     * Return the categories index.
     * @return array<string, Element>
     */
    function getCategoriesIndex(): array {
        return $this->categoryIndex;
    }

    /**
     * Return the menus index.
     * @return array<mixed>
     */
    function getMenusIndex(): array {
        return $this->menusIndex;
    }

    /**
     * Initialize the indexing system and create the indexes files if needed.
     */
    private function Initialize(): void {
        $this->tag2PostIndex = [];
        $this->category2PostsIndex = [];
        $this->menusIndex = [];
        if (\file_exists($this->root.DS.self::POST_INDEX_FILE) === false) {
            $this->reindex();
        } else {
            $this->categoryIndex = $this->loadIndex($this->root.DS.self::CAT_INDEX_FILE);
            $this->postIndex = $this->loadIndex($this->root.DS.self::POST_INDEX_FILE);
            $this->pageIndex = $this->loadIndex($this->root.DS.self::PAGE_INDEX_FILE);
            $this->tagIndex = $this->loadIndex($this->root.DS.self::TAG_INDEX_FILE);
            $this->menusIndex = $this->loadIndex($this->root.DS.self::MENUS_INDEX_FILE);
        }
        $this->slugIndex = \array_merge($this->postIndex, $this->categoryIndex, $this->pageIndex, $this->tagIndex, $this->buildLandingIndex());
    }

    /**
     *
     * @return array<string, Element>
     */
    private function buildLandingIndex(): array {
        $index = [];
        $index[BLOG_INDEX_KEY] = $this->createElement($this->root.DS.self::POST_LANDING_FILE, BLOG_INDEX_KEY);
        $index[TAG_INDEX_KEY] = $this->createElement($this->root.DS.self::TAG_LANDING_FILE, TAG_INDEX_KEY);
        $index[CAT_INDEX_KEY] = $this->createElement($this->root.DS.self::CAT_LANDING_FILE, CAT_INDEX_KEY);
        return $index;
    }

    /**
     * Scans the <i>content/common/categories</i> folder creating and indexing all the files.
     * When the index is built, it is also loaded.
     * @return array<string, Element>
     */
    private function buildCategoryIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->categoriesDir.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $key = 'category'.FWD_SLASH.\pathinfo($filepath, PATHINFO_FILENAME);
            $index[$key] = $this->createElement($filepath, $key);
        }
        $this->writeIndex($this->root.DS.self::CAT_INDEX_FILE, $index);
        return $index;
    }

    /**
     * Scans the <i>content/pages</i> folder creating and indexing all the files and folders.
     * When the index is built, it is also loaded.
     * @return array<string, Element> Returns the page index.
     */
    private function buildPageIndex(): array {
        $menuIndex = [];
        $pageIndex = [];
        $len = \strlen($this->pagesDir) + 1;
        $pages = $this->scanDirectory($this->pagesDir);
        \sort($pages);
        foreach ($pages as $filepath) {
            $location = \substr($filepath, $len);
            $key = \str_replace(DS, FWD_SLASH, \substr($location, 0, (\strlen($location) - CONTENT_FILE_EXT_LEN)));
            $pageIndex[$key] = $this->createElement($filepath, $key);
            $menuIndex = $this->getMenuSettings($filepath, $key);
        }
        $this->writeIndex($this->root.DS.self::PAGE_INDEX_FILE, $pageIndex);
        $this->writeIndex($this->root.DS.self::MENUS_INDEX_FILE, $menuIndex);
        return $pageIndex;
    }

    /**
     * Scans the <i>content/user-data/[username]/posts</i> folder creating and indexing all files.
     * This method also creates the menu index, as the menus are based on the front matter of
     * page content files.
     * When the index is built, it is also loaded.
     * @return array<string, Element>
     */
    private function buildPostsIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->userDataDir.DS.'*'.DS.'posts'.DS.'*'.DS.'*'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $element = $this->createElement($filepath, \pathinfo($filepath, PATHINFO_FILENAME));
            if (!isset($element->key)) {
                throw new \RuntimeException("Invalid format of index element: " . print_r($element, true)); // @codeCoverageIgnore
            }
            $index[(string)$element->key] = $element;
        }
        $this->writeIndex($this->root.DS.self::POST_INDEX_FILE, $index);
        return $index;
    }

    /**
     * Scans through all the posts extracting the tags.
     * When the index is built, it is also loaded.
     * @param array<mixed> $tag2PostsIndex
     * @param array<mixed> $cat2PostIndex
     * @return array<string, Element>
     */
    private function buildTagIndex(array &$tag2PostsIndex, array &$cat2PostIndex): array {
        $index = [];
        foreach ($this->postIndex as $post) {
            if (!isset($post->key, $post->tags, $post->category)) {
                throw new \RuntimeException("Invalid format of index element: " . print_r($post, true)); // @codeCoverageIgnore
            }
            foreach ($post->tags as $tag) {
                $index[(string)$tag] = $this->createElementClass($tag, \ucfirst(\str_replace('-', ' ', $tag)), ENUM_TAG);
                $tag2PostsIndex[$tag][] = $post->key;
            }
            $cat2PostIndex[$post->category] = $post->key;
        }
        $this->writeIndex($this->root.DS.self::TAG_INDEX_FILE, $index);
        $this->writeIndex($this->root.DS.self::TAG2POSTS_INDEX_FILE, $tag2PostsIndex);
        $this->writeIndex($this->root.DS.self::CAT2POSTS_INDEX_FILE, $cat2PostIndex);
        return $index;
    }

    /**
     * Recursively scans a folder heirarchy.
     * @return array<int, string>
     */
    private function scanDirectory(string $rootDir): array {
        static $files = [];
        if (($handle = \opendir($rootDir)) === false) {
            throw new \ErrorException("opendir() failed [$rootDir]"); // @codeCoverageIgnore
        }
        while (($entry = \readdir($handle)) !== false) {
            $path = $rootDir.DS.$entry;
            if (\is_dir($path)) {
                if ($entry === '.' || $entry === '..') continue;
                $this->scanDirectory($path);
                continue;
            }
            $files[] = $path;
        }
        \closedir($handle);
        return $files;
    }

    /**
     * @return array<int, string>
     */
    private function parseDirectory(string $pattern): array {
        if (($files = \glob($pattern, GLOB_NOSORT)) === false) {
            throw new \RuntimeException("glob() failed [$pattern]"); // @codeCoverageIgnore
        }
        return $files;
    }

    /**
     * Parses the given filepath parameter and creates a <code>stdClass</code> object to represent
     * an index element.
     * @param string $filepath
     * @param string $key Default is <code>null</code> and used with processing a post, otherwise
     * the key should be provided.
     * @throws \InvalidArgumentException
     * @return Element
     */
    private function createElement(string $filepath, string $key): Element {
        if (empty($key)) {
            throw new \RuntimeException("Key is empty for [$filepath]");
        }
        if (\strpos($filepath, self::CATEGORIES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_CATEGORY);
        }
        if (\strpos($filepath, self::PAGES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_PAGE);
        }
        if (\strpos($filepath, self::CAT_LANDING_FILE) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_CATEGORY);
        }
        if (\strpos($filepath, self::TAG_LANDING_FILE) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_TAG);
        }
        if (\strpos($filepath, self::POST_LANDING_FILE) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_POST);
        }
        if (\strlen($key) < 17) {
            throw new \InvalidArgumentException("Content filename is too short [$key]");
        }
        $dateString = \substr($key, 0, 14);
        $start = 15;
        if (($end = \strpos($key, '_', $start)) === false) {
            throw new \InvalidArgumentException("Content filename syntax error [$key]");
        }
        $tagList = \substr($key, $start, $end-$start);
        $title = \substr($key, $end + 1);
        $year = \substr($dateString, 0, 4);
        $month = \substr($dateString, 4, 2);
        $key = $year.FWD_SLASH.$month.FWD_SLASH.$title;
        $pathinfo = \pathinfo($filepath);
        $parts = \explode(DS, $pathinfo['dirname']);
        $cnt = \count($parts);
        return $this->createElementClass($key, $filepath, ENUM_POST, $parts[$cnt - 2], $parts[$cnt - 1], $parts[$cnt - 4], $dateString, $tagList);
    }

    /**
     * Creates and populates an index Element class.
     * @param string $key The index key
     * @param string $path The filepath
     * @param string $section 'pages', 'posts', 'categories' or 'tags'
     * @param string $category
     * @param string $type
     * @param string $username
     * @param string $date
     * @param string $tagList
     * @return Element stdClass
     */
    private function createElementClass(string $key, string $path = EMPTY_VALUE, string $section = EMPTY_VALUE, string $category = EMPTY_VALUE, string $type = EMPTY_VALUE, string $username = EMPTY_VALUE, string $date = EMPTY_VALUE, string $tagList = ''): Element {
        $tags = [];
        if (!empty($tagList)) {
            $tags = \explode(',', $tagList);
        }
        $obj = new Element();
        $obj->key = $key;
        $obj->path = $path;
        $obj->section = $section;
        $obj->category = $category;
        $obj->type = $type;
        $obj->username = $username;
        $obj->date = $date;
        $obj->tags = $tags;
        return $obj;
    }

    /**
     * Writes data to an index file, creating the file if necessary.
     * @param array<mixed> $index
     */
    private function writeIndex(string $filepath, array $index): void {
        $index = \serialize($index);
        if (\file_put_contents($filepath, print_r($index, true)) === false) {
            throw new \RuntimeException("file_put_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
    }

    /**
     * Reads the given index file and returns it as an array.
     * @return array<mixed>
     */
    private function loadIndex(string $filepath): array {
        if (\file_exists($filepath) === false) {
            throw new \InvalidArgumentException("Cannot load index file. Does not exist [$filepath]"); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($filepath)) === false) {
            throw new \ErrorException("file_get_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
        if (($data = \unserialize($data)) === false) {
            throw new \ErrorException("unserialize() failed [$filepath]"); // @codeCoverageIgnore
        }
        return $data;
    }

    /**
     * Reads the given file and creates an
     * @return array<mixed>
     */
    private function getMenuSettings(string $filepath, string $key): array {
        if (empty($key)) {
            throw new \RuntimeException("Key is empty for [$filepath]");
        }
        if (($json = \file_get_contents($filepath)) === false) {
            throw new \RuntimeException("file_get_contents() failed reading [$filepath]");
        }
        $data = \json_decode($json, true);
        if (isset($data['menus'])) {
            foreach($data['menus'] as $k => $v) {
                $v['key'] = $key;
                $data['menus'][$k] = $v;
            }
            return $data['menus'];
        }
        return [];
    }
}
