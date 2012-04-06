<?php

require_once __DIR__.'/../vendor/.composer/autoload.php';
require_once __DIR__.'/core.php';

use App\Core;
use Silex\Application;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SymfonyBridgesServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

$core = new Core(new Yaml());
$config = $core->getConfig(__DIR__.'/config/config.yml');
require_once __DIR__.'/autoload.php';

$app = new Application();
use Twig_Extension\AssetsExtension;

// Cache
$app['cache.path'] = __DIR__.'/cache';

// Http cache
$app['http_cache.cache_dir'] = $app['cache.path'].'/http';

// App debug mode
$app['debug'] = $config['framework']['env'] == 'prod' ? false : true;

/**
*   Load Extensions / Services
**/
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile'           => __DIR__.'/logs/'.$config['framework']['env'].'.log',
    'monolog.class_path'        => __DIR__.'/../vendor/monolog/src',
));
$app->register(new TwigServiceProvider(), array(
    'twig.path'       => array_merge(
        $core->getViews(), 
        array(
            __DIR__.'/../vendor/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views/Form',
        )),
    'twig.form.templates'       => array(),
    'twig.class_path'           => __DIR__.'/../vendor/Twig/lib',
));
$app->register(new SymfonyBridgesServiceProvider(), array(
   'symfony_bridges.class_path' => __DIR__.'/../vendor/symfony/src'
));
$app->register(new SwiftmailerServiceProvider(), array(
    'swiftmailer.options'       => $config['mailer'],
    'swiftmailer.class_path'    => __DIR__.'/../vendor/swiftmailer/swiftmailer/lib/classes',
));
$app->register(new DoctrineServiceProvider(), array(
    'db.options'                => $config['db'][$config['framework']['env']],
    'db.dbal.class_path'        => __DIR__.'/../vendor/dbal/lib',
    'db.common.class_path'      => __DIR__.'/../vendor/common/lib',
));

// be sure not to acccess db and mailer config elsewhere
unset($config['db']);
unset($config['mailer']);

/**
*   Load Twig extensions
**/
$oldConfigure = isset($app['twig.configure']) ? $app['twig.configure']: function(){};
$app['twig.configure'] = $app->protect(function($twig) use ($oldConfigure, $app) {
    $oldConfigure($twig);
    $twig->addExtension(new \Twig_Extensions_Extension_Text());
    $twig->addExtension(new AssetsExtension(str_replace('/index.php', '', $_SERVER['PHP_SELF']).'/assets'));
});

/**
*   Load translations
**/
$app->register(new TranslationServiceProvider());
$app['translator.messages'] = $core->getTranslations();
$app['locale_fallback'] = isset($config['framework']['locale_fallback']) ? $config['framework']['locale_fallback'] : 'en';

/**
*   Inject core in Silex app
**/
$app['core'] = $app->share(function() use($app, $core) {
    return $core;
});

/**
*   Load Apps Controllers
**/
$controllers = $core->getControllers();
foreach ($controllers as $controller => $file) {
    if (is_file($file)) {
        require $file;
    }
}

return $app;