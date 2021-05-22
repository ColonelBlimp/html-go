<?php declare(strict_types=1);
namespace html_go\model;

use html_go\indexing\Element;

if (!\defined('FM_DELIM')) {
    \define('FM_DELIM', '+++');
}

final class ModelFactory
{
    const KEY_MENUS_DATA = 'menus';
    const KEY_BODY_DATA = 'body';
    const KEY_LIST_DATA = 'list';
    const KEY_TITLE_DATA = 'title';
    const KEY_DESC_DATA = 'description';

    /**
     * @var Config $config
     */
    private Config $config;

    /**
     * ModelFactory constructor
     * @param Config $config
     */
    function __construct(Config $config) {
        $this->config = $config;
    }

    function createFromElement(Element $element): Content {
        return new Content($this->createSiteObject(), $element, $this->parseData($element));
    }

    /**
     * Create a <code>Content</code> object from a data array.
     * @param array<mixed> $data
     * @return Content
     */
    function createFromData(array $data): Content {
        return new Content($this->createSiteObject(), Element::createEmpty(),
            \array_merge($this->getInitializedData(), $data));
    }

    /**
     * @return array<string, mixed>
     */
    private function parseData(Element $element): array {
        $data = $this->getInitializedData();
        $file = new \SplFileObject($element->getPath());
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);
        if (($line = $file->fgets()) === false) {
            throw new \RuntimeException("SplFileObject::fgets() failed."); // @codeCoverageIgnore
        }
        if (FM_DELIM !== $line) {
            throw new \RuntimeException("A content file must begin with '" . FM_DELIM ."'"); // @codeCoverageIgnore
        }
        $fmProcess = true;
        while ($file->eof() === false) {
            if (($line = $file->fgets()) === false) {
                throw new \RuntimeException("SplFileObject::fgets() failed."); // @codeCoverageIgnore
            }
            if (FM_DELIM === $line) {
                $fmProcess = false;
                break;
            }
            $parts = \explode('=', $line);
            $menus = $this->extractMenuAndWeight($parts);
            if (!empty($menus)) {
                $data[self::KEY_MENUS_DATA] = $menus;
            } else {
                $data[self::KEY_MENUS_DATA] = [];
                $data[\trim($parts[0])] = \trim($parts[1], " \n\r\t\v\0\"\'");
            }
        }
        if ($fmProcess) {
            throw new \RuntimeException("Content file incorrectly formatted [" . $element->getPath() . "]"); // @codeCoverageIgnore
        }
        $len = $file->getSize() - $file->ftell();
        if (($body = $file->fread($len)) === false) {
            throw new \RuntimeException("SplFileObject::fread() failed."); // @codeCoverageIgnore
        }
        $data[self::KEY_BODY_DATA] = $body;
        $file = null;
        return $data;
    }

    /**
     * @param array<int, string> $parts
     * @return array<string, int>
     */
    private function extractMenuAndWeight(array $parts): array {
        $menus = [];
        foreach ($parts as $part) {
            $matches = [];
            $isMenu = \preg_match('/\[([^\]]*)\]/', $part, $matches);
            if ($isMenu === false || $isMenu === 0) continue;
            if (\count($matches) === 2) {
                $mparts = \explode(',', $matches[1]);
                foreach ($mparts as $menu) {
                    $items = \explode(':', \trim($menu, " \""));
                    $menus[$items[0]] = \intval($items[1]);
                }
            }
        }
        return $menus;
    }

    private function createSiteObject(): Site {
        static $site = null;
        if (empty($site)) {
            $site = new Site($this->config);
        }
        return $site;
    }

    private function getInitializedData(): array {
        return [
            self::KEY_MENUS_DATA => [],
            self::KEY_LIST_DATA => [],
            self::KEY_BODY_DATA => '',
            self::KEY_TITLE_DATA => '',
            self::KEY_DESC_DATA => ''
        ];
    }
}
