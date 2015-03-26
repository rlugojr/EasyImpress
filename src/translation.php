<?php
/*
* This file is part of the Orbitale EasyImpress package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/** @var Silex\Application $app */

$app['translator'] = $app->share($app->extend('translator', function(Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', TRANSDIR.'/fr.yml', 'fr');
    $translator->addResource('yaml', TRANSDIR.'/en.yml', 'en');
    return $translator;
}));

/** @var Translator $translator */
$translator = $app['translator'];
