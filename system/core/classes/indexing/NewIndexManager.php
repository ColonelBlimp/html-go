<?php declare(strict_types=1);
namespace html_go\indexing;

\define('ENUM_PAGE', 'page');
\define('ENUM_CATEGORY', 'category');
\define('ENUM_POST', 'post');
\define('ENUM_TAG', 'tag');

final class NewIndexManager
{
    private string $parentDir;
    private string $commonDir;
    private string $userDataDir;

    private string $pageInxFile;
    private string $catInxFile;
    private string $postInxFile;
    private string $tagInxFile;
    private string $tag2postInxFile;
    private string $cat2postInxFile;
    private string $menuInxFile;

    private array $catIndex;
    private array $pageIndex;
    private array $postIndex;
    private array $menuIndex;
    private array $tagIndex;

    function __construct(string $parentDir) {
        if (\is_dir($parentDir) === false) {
            throw new \InvalidArgumentException("The application root cannot be found [$parentDir]");
        }
        if (($path = \realpath($parentDir)) === false) {
            throw new \RuntimeException("realpath() function failed on [$parentDir]");
        }
        $indexDir = $path.DS.'cache'.DS.'indexes';
        $this->parentDir = $path;
        $this->commonDir = $path.DS.'content'.DS.'common';
        $this->userDataDir = $path.DS.'content'.DS.'user-data';

        $this->pageInxFile = $indexDir.DS.'page.inx';
        $this->catInxFile = $indexDir.DS.'category.inx';
        $this->postInxFile = $indexDir.DS.'post.inx';
        $this->tagInxFile = $indexDir.DS.'tag.inx';
        $this->tag2postInxFile = $indexDir.DS.'tag2post.inx';
        $this->cat2postInxFile = $indexDir.DS.'cat2post.inx';
        $this->menuInxFile = $indexDir.DS.'menu.inx';

        $this->initialize();
    }

    function reindex(): void {
        $this->catIndex = $this->buildCategoryIndex();
        $this->pageIndex = $this->buildPageAndMenuIndexes()[0];
        $this->menuIndex = $this->buildPageAndMenuIndexes()[1];
        $this->postIndex = $this->buildPostIndex();
        $this->tagIndex = $this->buildCompositeIndexes()[0];
        print_r($this->tagIndex);
    }

    /**
     * @return array<Element>
     */
    private function buildCategoryIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->commonDir.DS.'category'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $root = \substr($filepath, \strlen($this->commonDir) + 1);
            $key = \str_replace(DS, FWD_SLASH, \substr($root, 0, \strlen($root) - CONTENT_FILE_EXT_LEN));
            $index[$key] = $this->createElement($key, $filepath, ENUM_CATEGORY);
        }
        // Add the landing page
        $index['category'] = $this->createElement('category', $this->commonDir.DS.'landing'.DS.'category'.DS.'index.md', ENUM_CATEGORY);
        $this->writeIndex($this->catInxFile, $index);
        return $index;
    }

    private function buildPageAndMenuIndexes(): array {
        $menuIndex = [];
        $pageIndex = [];
        $pageDir = $this->commonDir.DS.'pages';
        $len = \strlen($pageDir) + 1;
        $pages = $this->scanDirectory($pageDir);
        \sort($pages);
        foreach ($pages as $filepath) {
            $location = \substr($filepath, $len);
            $key = \str_replace(DS, FWD_SLASH, \substr($location, 0, (\strlen($location) - CONTENT_FILE_EXT_LEN)));
            if (\str_ends_with($key, '/index')) {
                $key = substr($key, 0, \strlen($key) - 6);
            } else {
                if ($key === 'index') {
                    $key = FWD_SLASH;
                }
            }
            $pageIndex[$key] = $this->createElement($key, $filepath, ENUM_PAGE);
            $menuIndex = $this->getMenuSettings($key, $filepath);
        }
        $this->writeIndex($this->pageInxFile, $pageIndex);
        $this->writeIndex($this->menuInxFile, $menuIndex);
        return [$pageIndex, $menuIndex];
    }

    private function buildPostIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->userDataDir.DS.'*'.DS.'posts'.DS.'*'.DS.'*'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $key = \pathinfo($filepath, PATHINFO_FILENAME);
            $element = $this->createElement($key, $filepath, ENUM_POST);
            $index[(string)$element->key] = $element;
        }
        $index['blog'] = $this->createElementClass('blog', $this->commonDir.DS.'landing'.DS.'blog'.DS.'index.md', ENUM_POST);
        $this->writeIndex($this->postInxFile, $index);
        return $index;
    }

    /**
     * Reads the given file and creates an
     * @return array<mixed>
     */
    private function getMenuSettings(string $key, string $filepath): array {
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

    private function buildCompositeIndexes(): array {
        $tagIndex = [];
        $tag2PostsIndex = [];
        $cat2PostIndex = [];
        foreach ($this->postIndex as $post) {
            if (!isset($post->key, $post->tags, $post->category)) {
                throw new \RuntimeException("Invalid format of index element: " . print_r($post, true)); // @codeCoverageIgnore
            }
            foreach ($post->tags as $tag) {
                $tagIndex[(string)$tag] = $this->createElementClass($tag, \ucfirst(\str_replace('-', ' ', $tag)), ENUM_TAG);
                $tag2PostsIndex[$tag][] = $post->key;
            }
            $cat2PostIndex[$post->category] = $post->key;
        }
        $this->writeIndex($this->tagInxFile, $tagIndex);
        $this->writeIndex($this->tag2postInxFile, $tag2PostsIndex);
        $this->writeIndex($this->cat2postInxFile, $cat2PostIndex);
        return [$tagIndex, $tag2PostsIndex, $cat2PostIndex];
    }

    private function initialize(): void {
        if ((\is_dir($this->parentDir.DS.'cache'.DS.'indexes')) === false) {
            $dir = $this->parentDir.DS.'cache'.DS.'indexes';
            if (\mkdir($dir, MODE, true) === false) {
                throw new \RuntimeException("Unable to create directory [$dir]"); // @codeCoverageIgnore
            }
            $this->reindex();
        } else {
echo '1';
        }
    }

    private function loadIndex(string $filename): array {
        if (\file_exists($filename) === false) {
            throw new \RuntimeException("Index file does not exist [$filename]. Call 'redindex()'");
        }
        if (($data = \file_get_contents($filename)) === false) {
            throw new \ErrorException("file_get_contents() failed [$filename]"); // @codeCoverageIgnore
        }
        if (($data = \unserialize($data)) === false) {
            throw new \ErrorException("unserialize() failed [$filename]"); // @codeCoverageIgnore
        }
        return $data;
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
     * Writes data to an index file, creating the file if necessary.
     * @param array<mixed> $index
     */
    private function writeIndex(string $filepath, array $index): void {
        $index = \serialize($index);
        if (\file_put_contents($filepath, print_r($index, true)) === false) {
            throw new \RuntimeException("file_put_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
    }

    private function createElement(string $key, string $filepath, string $section): Element {
        if (empty($key)) {
            throw new \RuntimeException("Key is empty for [$filepath]");
        }
        switch ($section) {
            case ENUM_CATEGORY:
            case ENUM_PAGE:
                return $this->createElementClass($key, $filepath, $section);
            case ENUM_POST:
                if (\strlen($key) < 17) {
                    throw new \InvalidArgumentException("Post content filename is too short [$key]");
                }
                $pathinfo = \pathinfo($filepath);
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
                $parts = \explode(DS, $pathinfo['dirname']);
                $cnt = \count($parts);
                return $this->createElementClass($key, $filepath, ENUM_POST, $parts[$cnt - 1], $parts[$cnt - 2], $parts[$cnt - 4], $dateString, $tagList);
            default:
                throw new \RuntimeException("Unknown sectiontype [$section]");
        }
    }

    /**
     * Creates and populates an index Element class.
     * @param string $key The index key
     * @param string $path The filepath
     * @param string $section 'pages', 'posts', 'categories' or 'tags'
     * @param string $category
     * @param string $username
     * @param string $date
     * @param string $tagList
     * @return Element stdClass
     */
    private function createElementClass(string $key, string $path, string $section, string $type = EMPTY_VALUE, string $category = EMPTY_VALUE, string $username = EMPTY_VALUE, string $date = EMPTY_VALUE, string $tagList = ''): Element {
        $tags = [];
        if (!empty($tagList)) {
            $tags = \explode(',', $tagList);
        }
        $obj = new Element();
        $obj->key = $key;
        $obj->path = $path;
        $obj->type = $type;
        $obj->section = $section;
        $obj->category = $category;
        $obj->username = $username;
        $obj->date = $date;
        $obj->tags = $tags;
        return $obj;
    }
}
