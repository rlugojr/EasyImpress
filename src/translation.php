<?php
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Silex\Application;

/** @var Silex\Application $app */

$app['translator'] = $app->share($app->extend('translator', function(Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', TRANSDIR.'/fr.yml', 'fr');
    $translator->addResource('yaml', TRANSDIR.'/en.yml', 'en');
    return $translator;
}));

/** @var Translator $translator */
$translator = $app['translator'];