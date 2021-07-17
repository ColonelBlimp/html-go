<?php declare(strict_types=1);
namespace html_go\model;

abstract class AdminModelFactory
{
    protected Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * Create a content object (stdClass) specifically for the admin console.
     * @param array<string> $params When populating with variable arguments, use the following
     * <b>named parameters<b>:
     * <ul>
     *   <li>title:</li>
     * </ul>
     * @return \stdClass
     */
    public function createAdminContentObject(array $params): \stdClass {
        $contentObject = new \stdClass();
        $contentObject->site = $this->getSiteObject();
        $contentObject->title = $this->checkSet('title', $params);
        $contentObject->context = $this->checkSet('context', $params);
        $contentObject->template = $this->checkSet('template', $params);
        return $contentObject;
    }

    /**
     *
     * @param string $key
     * @param array<string> $params
     * @throws \InvalidArgumentException
     * @return string
     */
    private function checkSet(string $key, array $params): string {
        if (empty($params[$key])) {
            throw new \InvalidArgumentException("The '$key:' parameter has not been set!");
        }
        return $params[$key];
    }

    protected function getSiteObject(): \stdClass {
        static $site = null;
        if (empty($site)) {
            $site = new \stdClass();
            $site->url = $this->config->getString(Config::KEY_SITE_URL);
            $site->name = $this->config->getString(Config::KEY_SITE_NAME);
            $site->title = $this->config->getString(Config::KEY_SITE_TITLE);
            $site->description = $this->config->getString(Config::KEY_SITE_DESCRIPTION);
            $site->tagline = $this->config->getString(Config::KEY_SITE_TAGLINE);
            $site->copyright = $this->config->getString(Config::KEY_SITE_COPYRIGHT);
            $site->language = $this->config->getString(Config::KEY_LANG);
            $site->theme = $this->config->getString(Config::KEY_THEME_NAME);
            $site->tpl_engine = $this->config->getString(Config::KEY_TPL_ENGINE);
        }
        return $site;
    }
}
