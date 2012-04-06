<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class Core
{
    private static $yaml;

    protected $namespaces;
    protected $views;
    protected $controllers;
    protected $translations;

    public function __construct(Yaml $yaml)
    {
        self::$yaml = $yaml;

        $this->namespacesGestion();
        $this->controllersGestion();
        $this->viewsGestion();
        $this->translationsGestion();
    }

    public function getNamespaces()
    {
        return $this->namespaces;
    }

    public function setNamespaces(array $namespaces = array())
    {
        $this->namespaces = $namespaces;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews(array $views = array())
    {
        $this->views = $views;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function setControllers(array $controllers = array())
    {
        $this->controllers = $controllers;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations(array $translations = array())
    {
        $this->translations = $translations;
    }

    public function getConfig($file)
    {
        return self::$yaml->parse($file);
    }

    private function namespacesGestion()
    {
        $apps_namespaces = array();
        $apps = scandir(__DIR__.'/../src');
        clearstatcache();
        foreach ($apps as $namespace) {
            if ($namespace != '.' && $namespace != '..' && is_dir(__DIR__.'/../src/'.$namespace)) {
                $apps_namespaces[$namespace] = __DIR__.'/../src';
            }
        }
        $this->setNamespaces($apps_namespaces);
    }

    private function controllersGestion()
    {
        $apps_controllers = array();
        foreach ($this->namespaces as $namespace => $dir) {
            if (is_dir($dir.'/'.$namespace.'/Controller')) {
                $controllers = scandir($dir.'/'.$namespace.'/Controller');
                foreach ($controllers as $controller) {
                    if (strpos($controller, 'Controller.php') !== false) {
                        $apps_controllers[$namespace.'\Controller\\'.$controller] = $dir.'/'.$namespace.'/Controller/'.$controller;
                    }
                }
            }
        }
        $this->setControllers($apps_controllers);
    }

    private function viewsGestion()
    {
        $apps_views = array();
        foreach ($this->namespaces as $namespace => $dir) {
            if (is_dir($dir.'/'.$namespace.'/Resources/views')) {
                $apps_views[] = $dir.'/'.$namespace.'/Resources/views';
            }
        }
        $this->setViews($apps_views);
    }

    private function translationsGestion()
    {
        $apps_translations = array();
        foreach ($this->namespaces as $namespace => $dir) {
            if (is_dir($dir.'/'.$namespace.'/Resources/translations')) {
                $messages = scandir($dir.'/'.$namespace.'/Resources/translations');
                foreach ($messages as $message) {
                    if (preg_match('/^messages.[a-z]{2}.yml/i', $message)) {
                        $apps_translations[substr($message, 9, 2)] = !isset($apps_translations[substr($message, 9, 2)]) ? self::$yaml->parse($dir.'/'.$namespace.'/Resources/translations/'.$message) : array_merge($apps_translations[substr($message, 9, 2)], self::$yaml->parse($dir.'/'.$namespace.'/Resources/translations/'.$message));
                    }
                }
            }
        }
        $this->translations = $apps_translations;
    }
}