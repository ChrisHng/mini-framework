<?php

use Simplex\Framework;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$sc = new Symfony\Component\DependencyInjection\ContainerBuilder();
$sc->register('context', RequestContext::class);
$sc->register('matcher', UrlMatcher::class)
  ->setArguments([$routes, new Reference('context')]);

$sc->register('request_stack', RequestStack::class);
$sc->register('controller_resolver', ControllerResolver::class);
$sc->register('argument_resolver', ArgumentResolver::class);

$sc->register('listener.router', RouterListener::class)
  ->setArguments([new Reference('matcher'), new Reference('request_stack')]);

$sc->register('listener.response', ResponseListener::class)
  ->setArguments(['UTF-8']);

$sc->register('listener.exception', ExceptionListener::class)
  ->setArguments(['Calendar\Controller\ErrorController::exceptionAction']);

$sc->register('dispatcher', EventDispatcher::class)
  ->addMethodCall('addSubscriber', [new Reference('listener.router')])
  ->addMethodCall('addSubscriber', [new Reference('listener.response')])
  ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

$sc->register('framework', Framework::class)
  ->setArguments([
    new Reference('dispatcher'),
    new Reference('controller_resolver'),
    new Reference('request_stack'),
    new Reference('argument_resolver')
    ]
  );

return $sc;
