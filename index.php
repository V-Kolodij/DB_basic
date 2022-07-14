<?php

require_once 'bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

$request = new Request(
    $_GET,
    $_POST,
    [],
    $_COOKIE,
    $_FILES,
    $_SERVER
);

$time = $request->cookies->get('time');

if ($time) {
    $timeSecondDiff = time() - $time;
    echo "<H1>You are have been visited the site {$timeSecondDiff} seconds</H1>";
}

$response = new Response(
    'Content',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);
if (!$time) {
    $response->headers->setCookie(Cookie::create('time', time()));
    $response->send();
}

require_once 'process.php';
