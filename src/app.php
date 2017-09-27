<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LeapYearController {

  public function indexAction($year) {
    if (is_leap_year($year)) {
      return new Response('Yep, this is a leap year');
    }

    return new Response('Nope, this is not a leap year');
  }
}

function is_leap_year($year = NULL) {
  if ($year === null) {
    $year = date('Y');
  }

  return 0 === $year % 400 || (0 === $year % 4 && 0 !== $year % 100);
}


$routes = new RouteCollection();
$routes->add(
  'leap_year',
  new Route('/is_leap_year/{year}',[
    'year' => null,
    '_controller' => [new LeapYearController(), 'indexAction'],
    ]
  )
);

return $routes;
