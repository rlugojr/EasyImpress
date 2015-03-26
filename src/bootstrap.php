<?php
/** @var Silex\Application $app */

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Binfo\Silex\MobileDetectServiceProvider;
use Symfony\Component\Routing\Generator\UrlGenerator;

$app['debug'] = APPDEBUG;
$app->register(new UrlGeneratorServiceProvider());
$app->register(new TranslationServiceProvider(), array('locale_fallbacks' => array(DEFAULT_LOCALE),));
$app->register(new MobileDetectServiceProvider());

include 'twig.php';
include 'translation.php';
include 'Slider.php';
include 'Slide.php';

/** @var UrlGenerator $urlGenerator */
$urlGenerator = $app['url_generator'];

/** @var Mobile_Detect $mobileDetect */
$mobileDetect = $app['mobile_detect'];
