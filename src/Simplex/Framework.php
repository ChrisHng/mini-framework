<?php

namespace Simplex;


use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Framework {
  protected $matcher;
  protected $controllerResolver;
  protected $argumentResolver;
  protected $dispatcher;

  public function __construct(UrlMatcher $matcher, ControllerResolver $controllerResolver, ArgumentResolver $argumentResolver, EventDispatcher $dispatcher) {
    $this->matcher = $matcher;
    $this->controllerResolver = $controllerResolver;
    $this->argumentResolver = $argumentResolver;
    $this->dispatcher = $dispatcher;
  }

  public function handle(Request $request) {
    $this->matcher->getContext()->fromRequest($request);

    try {
      $request->attributes->add($this->matcher->match($request->getPathInfo()));

      $controller = $this->controllerResolver->getController($request);
      $arguments = $this->argumentResolver->getArguments($request, $controller);

      $response = call_user_func($controller, $arguments);
    }
    catch(Routing\Exception\ResourceNotFoundException $e) {
      $response = new Response('Not Found', 404);
    }
    catch (Exception $e) {
      $response = new Response($e->getMessage(), 500);
    }

    $this->dispatcher->dispatch('response', new ResponseEvent($request ,$response));

    return $response;
  }
}


