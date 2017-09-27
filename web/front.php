<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/app.php';

$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

try {
  $request->attributes->add($matcher->match($request->getPathInfo()));


  $controller = $controllerResolver->getController($request);
  $arguments = $argumentResolver->getArguments($request, $controller);

  $response = call_user_func($controller, $arguments);
}
catch(Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
}
catch (Exception $e) {
    $response = new Response($e->getMessage(), 500);
}


$response->send();

