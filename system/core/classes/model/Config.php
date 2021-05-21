<?php declare(strict_types=1);
namespace html_go\model;

final class Config
{
    const KEY_SITE_URL = 'site.url';
    const KEY_TITLE = 'site.title';
    const KEY_DESCRIPTION = 'site.description';
    const KEY_TAGLINE = 'site.tagline';
    const KEY_COPYRIGHT = 'site.copyright';
    const KEY_LANG = 'site.language';
    const KEY_TPL_ENGINE = 'template.engine';
    const KEY_TPL_CACHING = 'template.engine.caching';
    const KEY_TPL_FILE_EXT = 'template.engine.file.ext';
    const KEY_TPL_STRICT_VARS_TWIG = 'template.engine.twig.strict_variables';
    const KEY_THEME_NAME = 'theme.name';

    /**
     * @var array<string, string>
     */
    private array $config;

    /**
     * Config constructor.
     * @param string $configRoot The root directory containing the 'config.ini' file.
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    function __construct(string $configRoot) {
        $configFile = $configRoot.DS.'config.ini';
        if (!\is_file($configFile)) {
            throw new \InvalidArgumentException("Configuration INI file not found [$configFile]");
        }
        if (($config = \parse_ini_file($configFile, false, INI_SCANNER_TYPED)) === false) {
            throw new \RuntimeException("parse_ini_file() failed [$configFile]");
        }
        $this->config = $this->validateConfig($config);
    }

    function getString(string $key, string $default = ''): string {
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
     * Check required options are set and set defaults.
     * @param array<string, string> $config
     * @return array<string, mixed>
     */
    private function validateConfig(array $config): array {
        if (isset($config[Config::KEY_SITE_URL]) === false) {
            throw new \RuntimeException("Configuration option 'site.url' not set.");
        }
        if (isset($config[Config::KEY_TITLE]) === false) {
            $config[Config::KEY_TITLE] = 'HTML-go';
        }
        if (isset($config[Config::KEY_DESCRIPTION]) === false) {
            $config[Config::KEY_DESCRIPTION] = 'Powered by HTML-go, a databaseless, flat-file blogging platform';
        }
        if (isset($config[Config::KEY_TAGLINE]) === false) {
            $config[Config::KEY_TAGLINE] = 'Another HTML-go Site';
        }
        if (isset($config[Config::KEY_COPYRIGHT]) === false) {
            $config[Config::KEY_COPYRIGHT] = '(c) Copyright, Your Name';
        }
        if (isset($config[Config::KEY_LANG]) === false) {
            $config[Config::KEY_LANG] = 'en';
        }
        if (isset($config[Config::KEY_TPL_ENGINE]) === false) {
            $config[Config::KEY_TPL_ENGINE] = 'twig';
        }
        if (isset($config[Config::KEY_TPL_CACHING]) === false) {
            $config[Config::KEY_TPL_CACHING] = false;
        }
        if (isset($config[Config::KEY_TPL_FILE_EXT]) === false) {
            $config[Config::KEY_TPL_FILE_EXT] = 'twig';
        }
        if (isset($config[Config::KEY_TPL_STRICT_VARS_TWIG]) === false) {
            $config[Config::KEY_TPL_STRICT_VARS_TWIG] = true;
        }
        if (isset($config[Config::KEY_THEME_NAME]) === false) {
            $config[Config::KEY_THEME_NAME] = 'default';
        }
        return $config;
    }
}
