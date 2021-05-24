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
            throw new \RuntimeException("Object parameter not an instance of stdClass: " . print_r($obj, true)); // @codeCoverageIgnore
        }
        //TODO: Load the file and add the data to the $obj
        echo 'Before: '.print_r($obj, true);
        $obj = $this->loadDataFile($obj);
        echo 'After: '.print_r($obj, true);

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
     * Creates a custom object whose data is NOT loaded from the filesystem.
     * @param mixed ...$args
     * @return object stdClass
     */
    private function createObject(...$args): object {
        $obj = (object)$args;
//        print_r($obj);
        return $obj;
    }

    private function loadDataFile(object $stdClass): object {
        if (!isset($stdClass->path)) {
            throw new \RuntimeException("Object does not have 'path' property " . print_r($stdClass, true));
        }
        if (($data = \file_get_contents($stdClass->path)) === false) {
            throw new \RuntimeException("file_get_contens() failed opening [$stdClass->path]");
        }

        $stdClass = $this->parseFrontMatter($stdClass, $data);

        return $stdClass;
    }

    /**
     * Parse the front matter and added the key/value pairs to the given stdClass, which is
     * then returned.
     * @param object $stdClass
     * @param string $data
     * @throws \RuntimeException
     */
    private function parseFrontMatter(object $stdClass, string $data): object {
        $str = \str_replace(["\n\r", "\r\n"], "\n", $data);
        $str = \str_replace("\t", " ", $str);
        $lines = \explode("\n", $data);
        $cnt = \count($lines);
        $start = false;
        for ($index = 0; $index < $cnt; $index++) {
            $line = trim($lines[$index]);
            if ($line === '+++') {
                if ($start === false) {
                    $start = true;
                    continue;
                } else {
                    break;
                }
            }

            if (empty($line) || $line[0] === '#') {
                continue;
            }

            if (\strpos($line, '=')) {
                $kv = \explode('=', $line, 2);
                $key = \trim($kv[0], " \n\r\t\v\0\"");
                $val = \trim($kv[1], " \n\r\t\v\0\"");
                $stdClass->$key = $val;
            }
        }
        return $stdClass;
    }
}
