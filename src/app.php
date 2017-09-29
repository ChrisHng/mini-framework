<?php

namespace Calendar;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('leap_year', new Route('/is_leap_year/{year}',[
    'year' => null,
    '_controller' => 'Calendar\Controller\LeapYearController::indexAction',
    ]
  )
);

return $routes;
