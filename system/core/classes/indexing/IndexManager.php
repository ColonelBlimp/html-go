<?php declare(strict_types=1);
namespace html_go\indexing;

if (!\defined('MODE')) {
    \define('MODE', 0777);
}
if (!\defined('EMPTY_VALUE')) {
    \define('EMPTY_VALUE', '<empty>');
}
//TODO: Replace this with ENUMS from php 8.1
\define('ENUM_PAGE', 'pages');
\define('ENUM_CATEGORY', 'categories');
\define('ENUM_POST', 'posts');
\define('ENUM_TAG', 'tags');

final class IndexManager
{
    private const CATEGORIES_DIR = 'content'.DS.'common'.DS.'categories';
    private const PAGES_DIR = 'content'.DS.'common'.DS.'pages';
    private const INDEX_DIR = 'cache'.DS.'indexes';
    private const USER_DATA_DIR = 'content'.DS.'user-data';

    private string $appRoot;
    private string $slugIndexFilepath;
    private string $categoryToPostIndexFilepath;
    private string $postIndexFilepath;
    private string $pageIndexFilepath;
    private string $categoryIndexFilepath;
    private string $tagIndexFilepath;
    private string $tagToPostIndexFilepath;

    /**
     * @var array<string, Element> $slugIndex
     */
    private array $slugIndex;

    /**
     * @var array<string, array<int, string>> $categoryToPostIndex
     */
    private array $categoryToPostIndex;

    /**
     * @var array<string, array<int, string>> $tagToPostIndex
     */
    private array $tagToPostIndex;

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
     * IndexManager constructor.
     * @param string $appRoot The application's root. This class requires a particular
     * directory layout, thus the application root is passed in.
     * @throws \InvalidArgumentException
     */
    function __construct(string $appRoot) {
        if (($tmp = \realpath($appRoot)) === false) {
            throw new \InvalidArgumentException("Unable to validate the application root [$appRoot]");
        }
        $this->appRoot = $tmp;
        if (!\is_dir($this->appRoot.DS.IndexManager::CATEGORIES_DIR)) {
            $tmp = $this->appRoot.DS.IndexManager::CATEGORIES_DIR;
            throw new \RuntimeException(
                "Content directory format is invalid. Directory does not exist [$tmp]");
        }
        if (!\is_dir($this->appRoot.DS.IndexManager::PAGES_DIR)) {
            $tmp = $this->appRoot.DS.IndexManager::PAGES_DIR; // @codeCoverageIgnore
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$tmp]"); // @codeCoverageIgnore
        }
        if (!\is_dir($this->appRoot.DS.IndexManager::USER_DATA_DIR)) {
            $tmp = $this->appRoot.DS.IndexManager::USER_DATA_DIR; // @codeCoverageIgnore
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$tmp]"); // @codeCoverageIgnore
        }
        $this->init();
        $this->slugIndex = $this->loadIndex($this->slugIndexFilepath);
        $this->categoryToPostIndex = $this->loadIndex($this->categoryToPostIndexFilepath);
        $this->categoryIndex = $this->loadIndex($this->categoryIndexFilepath);
        $this->pageIndex = $this->loadIndex($this->pageIndexFilepath);
        $this->postIndex = $this->loadIndex($this->postIndexFilepath);
        $this->tagIndex = $this->loadIndex($this->tagIndexFilepath);
        $this->tagToPostIndex = $this->loadIndex($this->tagToPostIndexFilepath);
    }

    function reindex(): void {
        $categoryIndex = $this->fetchCategoryIndex();
        $postIndex = $this->fetchPostIndex();

        // Process the tag index
        $tagIndex = [];
        foreach ($postIndex as $post) {
            $tags = $post->getTags();
            foreach ($tags as $tag) {
                $title = \ucfirst(\str_replace('-', ' ', $tag));
                $tagIndex[$tag] = $this->createElementClass($tag, $title, ENUM_TAG);
            }
        }

        $pageIndex = $this->fetchPageIndex();
        $slugIndex = \array_merge($postIndex, $categoryIndex, $pageIndex, $tagIndex);

        $this->writeIndex($this->slugIndexFilepath, $slugIndex);
        $this->writeIndex($this->categoryIndexFilepath, $categoryIndex);
        $this->writeIndex($this->pageIndexFilepath, $pageIndex);
        $this->writeIndex($this->postIndexFilepath, $postIndex);
        $this->writeIndex($this->tagIndexFilepath, $tagIndex);
        $this->writeIndex($this->categoryToPostIndexFilepath, $this->buildCategoryToPostIndex($postIndex));
        $this->writeIndex($this->tagToPostIndexFilepath, $this->buildTagToPostIndex($postIndex));
    }

    /**
     * Use this method to search for a slug. All slugs are/must be unique. For posts and categories,
     * the slug is a simple string. However, for pages the slug includes the sub-directory
     * (if there is one). For example: 'apiaries/chilukwa' might be the slug,
     * while the content file would be '.../apiaries/chilukwa/_index.md'
     *
     * If two files define the same slug one will overwrite the other.
     *
     * @param string $key The slug
     * @return Element
     * @throws \RuntimeException if the given key is not found in the slug index
     */
    function getElementFromSlugIndex(string $key): Element {
        if (!isset($this->slugIndex[$key])) {
            throw new \RuntimeException("Key [$key] does not exist in the index. Use 'elementExists(...) before calling this method!");
        }
        return  $this->slugIndex[$key];
    }

    /**
     * Checks if an {@link Element} is already assigned to the given key. The slug index is searched.
     * @param string $key The slug
     * @return bool <code>true</code> if the key is assigned, otherwise <code>false</code>
     */
    function elementExists(string $key): bool {
        return isset($this->slugIndex[$key]);
    }

    /**
     * Returns a array of {@link Element} objects which represent posts assigned to the given
     * category slug (key).
     * @param string $key The category's slug
     * @return array<string, Element>|NULL if the $key (slug) does not exist in the system
     */
    function getPostsForCategory(string $key): ?array {
        if (!isset($this->categoryToPostIndex[$key])) {
            return null;
        }
        $postList = [];
        $postKeys = $this->categoryToPostIndex[$key];
        foreach ($postKeys as $key) {
//            if (($elem = $this->getElementFromSlugIndex($key)) === null) {
//                throw new \OutOfBoundsException("Indexes are inconsistent. The category key [$key] cannot be found in the slug index."); // @codeCoverageIgnore
//            }
//TODO: Refactor
            $elem = $this->getElementFromSlugIndex($key);
            $postList[$elem->getKey()] = $elem;
        }
        return $postList;
    }

    /**
     * Returns a array of {@link Element} objects which represent posts assigned to the given
     * tag slug (key).
     * @param string $key The tag's slug
     * @return array<string, Element>|NULL if the $key (slug) does not exist in the system
     */
    function getPostsForTag(string $key): ?array {
        if (!isset($this->tagToPostIndex[$key])) {
            return null;
        }
        $postList = [];
        $postKeys = $this->tagToPostIndex[$key];
        foreach ($postKeys as $key) {
            //            if (($elem = $this->getElementFromSlugIndex($key)) === null) {
            //                throw new \OutOfBoundsException("Indexes are inconsistent. The category key [$key] cannot be found in the slug index."); // @codeCoverageIgnore
            //            }
            //TODO: Refactor
            $elem = $this->getElementFromSlugIndex($key);
            $postList[$elem->getKey()] = $elem;
        }
        return $postList;
    }

    /**
     * Returns an unordered array of {@link Element} objects which represent all the posts
     * @return array<string, Element> Cannot be <code>null</code>
     */
    function getPostIndex(): array {
        return $this->postIndex;
    }

    /**
     * Returns an unordered array of {@link Element} objects which represent all the categories
     * @return array<string, Element> Cannot be <code>null</code>
     */
    function getCategoryIndex(): array {
        return $this->categoryIndex;
    }

    /**
     * Returns an unordered array of {@link Element} objects which represent all the pages
     * @return array<string, Element> Cannot be <code>null</code>
     */
    function getPageIndex(): array {
        return $this->pageIndex;
    }

    /**
     * Returns an unordered array of {@link Element} objects which represent all the tags
     * @return array<string, Element> Cannot be <code>null</code>
     */
    function getTagIndex(): array {
        return $this->tagIndex;
    }

    private function init(): void {
        $this->slugIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'slugindex.inx';
        $this->categoryToPostIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'cat2posts.inx';
        $this->pageIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'pages.inx';
        $this->postIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'posts.inx';
        $this->categoryIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'categories.inx';
        $this->tagIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'tags.inx';
        $this->tagToPostIndexFilepath = $this->appRoot.DS.IndexManager::INDEX_DIR.DS.'tag2posts.inx';

        if (!\file_exists($this->slugIndexFilepath)) {
            if (!\is_dir($this->appRoot.DS.IndexManager::INDEX_DIR)) {
                if (!\mkdir($this->appRoot.DS.IndexManager::INDEX_DIR, MODE, true)) {
                    $tmp = $this->appRoot.DS.IndexManager::INDEX_DIR; // @codeCoverageIgnore
                    throw new \ErrorException("mkdir() failed [$tmp]"); // @codeCoverageIgnore
                }
            }
            $this->reindex();
        }
    }

    /**
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
     * @param array<mixed> $data
     */
    private function writeIndex(string $filepath, array $data): void {
        $data = \serialize($data);
        if (\file_put_contents($filepath, print_r($data, true)) === false) {
            throw new \ErrorException("file_put_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
    }

    /**
     * @return array<string, array<string, Element>>
     */
    private function fetchPageIndex(): array {
        static $index = [];
        if (empty($index)) {
            $pagesRootDir = $this->appRoot.DS.IndexManager::PAGES_DIR;
            $len = \strlen($pagesRootDir) + 1;
            $pageIndex = $this->scanDirectory($pagesRootDir);
            \sort($pageIndex);

            foreach ($pageIndex as $filepath) {
                $key = \substr($filepath, $len);
                if (\str_ends_with($key, '_index.md')) {
                    $key = \substr($key, 0, -10);
                    if ($key === '') {
                        $key = 'home';
                    }
                } elseif (\str_ends_with($key, CONTENT_FILE_EXT)) {
                    $key = \substr($key, 0, -3);
                }
                $elem = $this->createElement($filepath, \str_replace(DS, '/', $key));
                $index[$elem->getKey()] = $elem;
            }
        }
        return $index;
    }

    /**
     * This method depends on {@link IndexManager::buildSlugIndex}
     * @param array<string, Element> $postList
     * @return array<string, array<int, string>>
     */
    private function buildCategoryToPostIndex(array $postList): array {
        static $index = [];
        foreach ($postList as $elem) {
            $index[$elem->getCategory()][] = $elem->getKey();
        }
        return $index;
    }

    /**
     * This method depends on {@link IndexManager::buildSlugIndex}
     * @param array<string, Element> $postList
     * @return array<string, array<int, string>>
     */
    private function buildTagToPostIndex(array $postList): array {
        static $index = [];
        foreach ($postList as $elem) {
            foreach ($elem->getTags() as $tag) {
                $index[$tag][] = $elem->getKey();
            }
        }
        return $index;
    }

    /**
     * @return array<string, Element>
     */
    private function fetchCategoryIndex(): array {
        static $list = [];
        if (empty($list)) {
            $list = $this->indexDirectory($this->appRoot.DS.IndexManager::CATEGORIES_DIR.DS.'*'.CONTENT_FILE_EXT);
        }
        return $list;
    }

    /**
     * @return array<string, Element>
     */
    private function fetchPostIndex(): array {
        static $list = [];
        if (empty($list)) {
            $list = $this->indexDirectory($this->appRoot.DS.IndexManager::USER_DATA_DIR.DS.
                '*'.DS.'posts'.DS.'*'.DS.'*'.DS.'*'.CONTENT_FILE_EXT);
        }
        return $list;
    }

    /**
     * @return array<int, string>
     */
    private function parseDirectory(string $pattern): array {
        if (($files = \glob($pattern, GLOB_NOSORT)) === false) {
            throw new \ErrorException("glob() failed [$pattern]"); // @codeCoverageIgnore
        }
        return $files;
    }

    /**
     * @param string $pattern
     * @throws \UnexpectedValueException
     * @return array<string, Element>
     */
    private function indexDirectory(string $pattern): array {
        $files = $this->parseDirectory($pattern);
        $index = [];
        foreach ($files as $file) {
            $elem = $this->createElement($file);
            $index[$elem->getKey()] = $elem;
        }
        return $index;
    }

    /**
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

    private function createElement(string $filepath, string $key = null): Element {
        $pathinfo = \pathinfo($filepath);
        if ($key === null) {
            $key = $pathinfo['filename'];
        }
        if (\strpos($filepath, IndexManager::CATEGORIES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_CATEGORY);
        }
        if (\strpos($filepath, IndexManager::PAGES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_PAGE);
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
        $key = \substr($key, $end + 1);
        $parts = \explode(DS, $pathinfo['dirname']);
        $cnt = \count($parts);

        return $this->createElementClass($key, $filepath, ENUM_POST, $parts[$cnt - 2],
            $parts[$cnt - 1], $parts[$cnt - 4], $dateString, $tagList);
    }

    private function createElementClass(
        string $key,
        string $path,
        string $section,
        string $category = EMPTY_VALUE,
        string $type = EMPTY_VALUE,
        string $username = EMPTY_VALUE,
        string $date = EMPTY_VALUE,
        string $tagList = ''): Element {
            $tags = [];
            if (!empty($tagList)) {
                $tags = \explode(',', $tagList);
            }
        return new Element($key, $path, $section, $category, $type, $username, $date, $tags);
    }
}
