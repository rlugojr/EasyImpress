<?php

use Silex\Application as App;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/constants.php';

$app = new App();

include APPDIR.'bootstrap.php';

$app->error(function (\Exception $e, $code) use ($app, $twig) {
    return $twig->render('error.html.twig', array(
        'exception' => $e,
        'code' => $code,
    ));
});

if (APPDEBUG === true) {
    $app->get('/delete_thumbs', function () use ($app, $twig, $urlGenerator) {

        $sliders = glob(SLIDESDIR.'*');

        foreach ($sliders as $slider) {
            if (is_dir($slider.'/img')) {
                $thumbs = glob($slider.'/img/*_thumb.jpg');
                foreach ($thumbs as $thumb) {
                    unlink($thumb);
                }
            }
        }

        return new RedirectResponse($urlGenerator->generate('home'));
    });
}

$app->get('/', function () use ($app, $twig) {
    $sliderName = 'home';

    if (!file_exists(SLIDESDIR.$sliderName.'/parameters.yml')) {
        throw new NotFoundHttpException('slider_not_found');
    }
    $slides = Yaml::parse(SLIDESDIR.$sliderName.'/parameters.yml');
    $slider = Slider::create($sliderName, $slides);
    return $twig->render('slider.html.twig', array('slider'=>$slider,'name'=>$sliderName));
})
->value('_locale', DEFAULT_LOCALE)
->assert('_locale', LOCALES)
->bind('home');

$app->get('/img/{sliderName}.{slideId}.jpg', function ($sliderName, $slideId, Request $request) use ($app, $urlGenerator, $mobileDetect) {

    $thumb = $request->query->has('thumbnail') && $request->query->get('thumbnail') === 'true';

    $lookingForReferer = $urlGenerator->generate('slider', array('sliderName' => $sliderName), UrlGenerator::ABSOLUTE_URL);
    $lookingForReferer = rtrim(preg_replace('~\?.*$~isUu', '', $lookingForReferer), '/');

    $actualReferer = preg_replace('~\?.*$~isUu', '', rtrim($request->headers->get('Referer'), '/'));

    $error = false;

    if ($actualReferer !== $lookingForReferer) {
        $error = $actualReferer !== str_replace('/home', '', $lookingForReferer);
    }

    if ($error === false) {

        $mobile = $mobileDetect->isMobile() || $mobileDetect->isTablet();

        $img = SLIDESDIR.$sliderName.'/img/'.$slideId.($mobile?'_mobile':'').'.jpg';
        $thumbFile = SLIDESDIR.$sliderName.'/img/'.$slideId.'_thumb.jpg';

        if (!file_exists($img) && $mobile) {
            $src = str_replace('_mobile', '', $img);
            $w = 1024;
            $h = 683;
            $command = '"'.CONVERT_PATH."\" \"$src\" -resize \"{$w}x{$h}\" -quality 80 \"$img\"";
            shell_exec($command);
        }

        if (file_exists($img)) {

            if ($thumb) {
                if (!file_exists($thumbFile)) {
                    if (!file_exists(SLIDESDIR.$sliderName.'/parameters.yml')) {
                        throw new NotFoundHttpException('slider_not_found');
                    }
                    $slides = Yaml::parse(SLIDESDIR.$sliderName.'/parameters.yml');
                    $slider = Slider::create($sliderName, $slides);
                    $conf = $slider->getConfig();
                    $w = isset($conf['thumbnails']['width']) ? (int) $conf['thumbnails']['width'] : 150;
                    $h = isset($conf['thumbnails']['height']) ? (int) $conf['thumbnails']['height'] : 150;
                    $command = '"'.CONVERT_PATH."\" -define jpeg:size={$w}x{$h} \"$img\" -thumbnail \"{$w}x{$h}^\" -gravity center -extent {$w}x{$h} \"$thumbFile\"";
                    shell_exec($command);
                }
                $content = file_get_contents($thumbFile);
            } else {
                $content = file_get_contents($img);
            }

            $response = new Response($content, 200);
            $response->headers->add(array('Content-Type' => 'image/jpeg',));
            $lastModified = new \Datetime();
            $lastModified->setTimestamp(filemtime($img));
            $response->setCache(array(
                'last_modified' => new \Datetime(),
                'max_age'       => 0,
                's_maxage'      => 0,
                'public'        => false,
            ));
            return $response;
        } else {
            $response = new Response('', 404);
            $response->setCache(array(
                'last_modified' => new \Datetime(),
                'max_age'       => 0,
                's_maxage'      => 0,
                'public'        => false,
            ));
            return $response;
        }
    } else {
        if ($actualReferer) {
            $response = new Response('', 403);
            $response->setCache(array(
                'last_modified' => new \Datetime(),
                'max_age'       => 0,
                's_maxage'      => 0,
                'public'        => false,
            ));
            return $response;
        } else {
            return new RedirectResponse($urlGenerator->generate('home'), 301);
        }
    }
})
->bind('sliderImg');

$app->get((USE_LOCALE ? '/{_locale}' : '').'/{sliderName}', function ($sliderName) use ($app, $twig, $urlGenerator) {

    $sliderName = rtrim($sliderName, '/');

    if ($sliderName === 'home') {
        return new RedirectResponse($urlGenerator->generate('home'));
    }

    if (!file_exists(SLIDESDIR.$sliderName.'/parameters.yml')) {
        throw new NotFoundHttpException('slider_not_found');
    }
    $slides = Yaml::parse(SLIDESDIR.$sliderName.'/parameters.yml');
    $slider = Slider::create($sliderName, $slides);
    return $twig->render('slider.html.twig', array('slider'=>$slider,'name'=>$sliderName));
})
->value('sliderName', 'home')->assert('sliderName', '\w+/?$')
->value('_locale', DEFAULT_LOCALE)->assert('_locale', LOCALES)
->bind('slider');

$app->run();
