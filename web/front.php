<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/app.php';

$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

try {
  $test = $matcher->match($request->getPathInfo());
  extract($test, EXTR_SKIP);
  include sprintf(__DIR__ . '/../src/pages/%s.php', $_route);
  $response = new Response(ob_get_clean());
}
catch(Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response($e->getMessage(), 500);
}

$response->send();
