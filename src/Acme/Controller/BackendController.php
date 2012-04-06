<?php

namespace Acme\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Index admin
 */
$app->get('/admin', function() use($app)
{
    echo 'Hello admin World!';
})
->bind('admin');