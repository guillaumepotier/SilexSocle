<?php

namespace Acme\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Index
 */
$app->get('/', function() use($app)
{
    return $app['twig']->render('Frontend\index.html.twig', array());
})
->bind('index');

/**
 * Hello Username
 */
$app->get('/hello/{username}', function($username) use($app)
{
    return $app['twig']->render('Frontend\hello.html.twig', array('username' => $username));
})
->value('username', 'guillaume')
->bind('hello');