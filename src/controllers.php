<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function (Request $request) use ($app) {
    return $app['twig']->render('index.html', array('base' => $request->getBasePath()));
})
->bind('homepage');

$app->get('/to_tifanofauvo',function (Request $request) use ($app){
    $originalText = $request->query->get('text');

    $translated = toTifanofauvo($originalText);
    return $app['twig']->render('index.html',
        array(
            'translated' => $translated,
            'original' => $originalText,
            'base' => $request->getBasePath()
        )
    );
});

$app->get('/dino_info',function (Request $request) use ($app){
    return $app['twig']->render('info.html',
        array(
            'base' => $request->getBasePath()
        )
    );
});

$app->get('/lipsum',function (Request $request) use ($app){
    $faker = Faker\Factory::create();
    $type = $request->query->get('type');
    $number = $request->query->get('number');

    switch ($type) {
        case 'paragraphs':
            $generated = $faker->paragraphs($number);
            $imploder = '&#10;';
            break;
        case 'phrases':
            $generated = $faker->sentences($number);
            $imploder = ' ';
            break;
        case 'words':
            $generated = $faker->words($number);
            $imploder = ' ';
            break;
        default:
            $generated = '';
            $imploder = '';
            break;
    }
    return $app['twig']->render('lipsum.html',
        array(
            'base' => $request->getBasePath(),
            'lorem' => $generated != '' ? toTifanofauvo(implode($imploder, $generated)) : ''
        )
    );
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

function toTifanofauvo($originalText){
    $words = ['ss','s','ra','p','j','gu','ci','g','qu','x','c', 'z'];
    $replacer = ['f','f','fa','f','v','fu','fi','f','f','f','f', 'v'];

    return str_replace($words, $replacer, $originalText);
}