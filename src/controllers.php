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
->bind('homepage')
;


$app->get('/to_tifanofauvo',function (Request $request) use ($app){
    $words = ['ss','s','ra','p','j','gu','ci','g','qu','x','c', 'z'];
    $replacer = ['f','f','fa','f','v','fu','fi','f','f','f','f', 'v'];
    $originalText = $request->query->get('text');

    $translated = str_replace($words, $replacer, $originalText);
    return $app['twig']->render('index.html',
        array(
            'translated' => $translated,
            'original' => $originalText,
            'base' => $request->getBasePath()
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
