<?php
/** @var Silex\Application $app */
use Silex\Provider\TwigServiceProvider;

/** @var Twig_Environment $twig */
$app->register(new TwigServiceProvider(), array(
    'twig.path' => VIEWSDIR,
    'debug' => APPDEBUG,
));

$app['twig'] = $app->share($app->extend('twig', function (Twig_Environment $twig, \Silex\Application $app) {

    /** Fonction "asset" */
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $app['request'];
        $base_url = $request->getSchemeAndHttpHost().$request->getBasePath();

        $url = sprintf('%s/%s', $base_url, ltrim($asset, '/'));

        return $url;
    }));

    /** Test "is numeric" */
    $twig->addTest(new \Twig_SimpleTest('numeric', function($var){
        return is_numeric($var);
    }));

    return $twig;
}));

$twig = $app['twig'];

/** @var Twig_Loader_Filesystem $twigLoader */
$twigLoader = $app['twig.loader.filesystem'];

/**
 * Ajout des dossiers des sliders dans Twig
 */
$sliders = glob(SLIDESDIR.'*');
foreach ($sliders as $dir) {
    $sliderName = basename($dir);
    if (is_dir($dir.'/views')) {
        $path = $dir.'/views';
        $twigLoader->addPath($path, 'Sliders:'.$sliderName);
    }
}
