<?php declare(strict_types=1);
namespace html_go\model;

final class Site
{
    function __construct(private Config $config) {
    }
    function getUrl(): string {
        return $this->config->getString(Config::KEY_SITE_URL);
    }
    function getTitle(): string {
        return $this->config->getString(Config::KEY_TITLE);
    }
    function getDescription(): string {
        return $this->config->getString(Config::KEY_DESCRIPTION);
    }
    function getTagline(): string {
        return $this->config->getString(Config::KEY_TAGLINE);
    }
    function getCopyright(): string {
        return $this->config->getString(Config::KEY_COPYRIGHT);
    }
}
