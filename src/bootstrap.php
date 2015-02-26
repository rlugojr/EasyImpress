<?php
/** @var Silex\Application $app */

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Binfo\Silex\MobileDetectServiceProvider;
use Symfony\Component\Routing\Generator\UrlGenerator;

$app['debug'] = APPDEBUG;
$app['locale'] = LOCALE;
$app->register(new UrlGeneratorServiceProvider());
$app->register(new TranslationServiceProvider(), array('locale_fallbacks' => array(LOCALE),));
$app->register(new MobileDetectServiceProvider());

include 'twig.php';
include 'translation.php';
include 'Impress.php';
include 'Slide.php';

/** @var UrlGenerator $urlGenerator */
$urlGenerator = $app['url_generator'];

/** @var Mobile_Detect $mobileDetect */
$mobileDetect = $app['mobile_detect'];
