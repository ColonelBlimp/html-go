<?php declare(strict_types=1);
namespace html_go\model;


final class ModelFactory
{
    function __construct(private Config $config) {
    }

    function create(object $obj): Content {
        if (!$obj instanceof \stdClass) {
            throw new \RuntimeException();
        }
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
