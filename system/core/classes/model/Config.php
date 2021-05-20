<?php declare(strict_types=1);
namespace html_go\model;

final class Config
{
    const KEY_SITE_URL = 'site.url';
    const KEY_COPYRIGHT = 'site.copyright';
    const KEY_DESCRIPTION = 'site.description';
    const KEY_TAGLINE = 'site.tagline';
    const KEY_TITLE = 'site.title';
    const KEY_TPL_ENGINE = 'template.engine';
    const KEY_TPL_CACHING = 'template.engine.caching';
    const KEY_TPL_STRICT_VARS_TWIG = 'template.engine.twig.strict_variables';
    const KEY_THEME_NAME = 'theme.name';

    /**
     * @var array<string, string>
     */
    private array $config;

    function __construct(string $iniFilepath) {
        if (!\is_file($iniFilepath)) {
            throw new \RuntimeException("Configuration INI file not found [$iniFilepath]");
        }
        if (($config = \parse_ini_file($iniFilepath, false, INI_SCANNER_TYPED)) === false) {
            throw new \RuntimeException("parse_ini_file() failed [$iniFilepath]");
        }
        $this->config = $this->validateConfig($config);
    }

    function getString(string $key, string $default = null): string {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_string($var) === false) {
            return $default;
        }
        return $var;
    }

    function getInt(string $key, int $default = -1): int {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_int($var) === false) {
            return $default;
        }
        return $var;
    }

    function getBool(string $key, bool $default = false): bool {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_bool($var) === false) {
            return $default;
        }
        return $var;
    }

    private function checkAndGet(string $key): mixed {
        if (isset($this->config[$key]) === false) {
            return null;
        }
       return $this->config[$key];
    }

    /**
     * @param array<string, string> $config
     * @return array<string, string>
     */
    private function validateConfig(array $config): array {
        if (isset($config[Config::KEY_SITE_URL]) === false) {
            throw new \RuntimeException("Configuration option 'site.url' not set.");
        }
        if (isset($config[Config::KEY_COPYRIGHT]) === false) {
            $config[Config::KEY_COPYRIGHT] = '(c) Copyright, Your Name';
        }
        if (isset($config[Config::KEY_TAGLINE]) === false) {
            $config[Config::KEY_TAGLINE] = 'Another HTML-go Site';
        }
        if (isset($config[Config::KEY_TITLE]) === false) {
            $config[Config::KEY_TITLE] = 'HTML-go';
        }
        if (isset($config[Config::KEY_DESCRIPTION]) === false) {
            $config[Config::KEY_DESCRIPTION] = 'Powered by HTML-go, a flatfile blogging platform';
        }
        return $config;
    }
}
