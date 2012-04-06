<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array_merge(array(
            'Twig_Extension'    => __DIR__.'/Resources',
        ),
        $core->getNamespaces()
    )
);
$loader->register();