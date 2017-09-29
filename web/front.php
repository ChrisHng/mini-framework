<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\StreamedResponseListener;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$requestStack = new RequestStack();
$routes = include __DIR__ . '/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

$listener = new ExceptionListener(
  'Calendar\Controller\ErrorController:exceptionAction'
);
$dispatcher->addSubscriber($listener);
$dispatcher->addSubscriber(new ResponseListener('UTF-8'));
$dispatcher->addSubscriber(new StreamedResponseListener());
$dispatcher->addSubscriber(new Simplex\StringResponseListener());

$framework = new Simplex\Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);

$response = $framework->handle($request)->send();
