# SilexSocle

This project is a base for my Silex pet-projects.

It embed:

* Twitter Bootstrap for Front integration
* Silex extensions I frequently use:
    * Twig
    * Translation
    * Monolog
    * SwiftMailer
    * Doctrine
    * SymfonyBridge
    * Session
    * UrlGenerator


# Installation

## getcomposer (if you don't already have)
*  `curl -s http://getcomposer.org/installer | php`
*  `php composer.phar install`

## chmod right dirs
*  `chmod 777 -R app/cache/ app/log/ web/assets/`

## fetch submodules
*  `git submodule update --init`

## tweak settings
*  `cp app/config/config.yml.dist app/config/config.yml`
Customize your settings in `app/config/config.yml`

## build jQuery
* `cd web/assets/jQuery`
* `make`


# Usage

* Put your projects in `src/` (ie: `src/Acme`)
* Your project arborescence must be like:
    * YourProject/
        * Controller
        * Ressources
            * translations
            * views

You can look at Acme demo project to see how it works


# Todo

* Tests
* Better twig views gestion (you currently cannot have to twig templates with same name in your different projects)
* Add a Form example
* Add cache config
* Add after/before convention


# Help

* http://silex.sensiolabs.org/documentation
* https://github.com/jquery/jquery