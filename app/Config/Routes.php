<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);
$routes->get('register', 'Register::index');
$routes->get('login', 'Login::index');
$routes->get('/', 'Home::index');
