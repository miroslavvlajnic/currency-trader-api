<?php

$router = new Core\Router;

/**
 * Route pattern (first argument) must begin with a forward slash (/) and 
 * must not have one at the end...
 * dynamic arguments are denoted with a colon sign (:)
 * e.g. '/user/:id'
 * 
 * examples of valid routes: '/user', '/user/:id', '/user/:id/posts'
 * examples of invalid routes: 'user', '/user/', 'user/:id', '/user/:id/posts/'
 * 
 * second argument must be an associative array with
 * two keys: 'controller' and 'action' 
 * 
 * they determine what method on which controller will be invoked
 * when the incoming request matches the route in question
 */

$router->post('/calculate', ['controller' => 'App\Controller\OrderController', 'action' => 'calculate']);
$router->get('/currencies', ['controller' => 'App\Controller\CurrenciesController', 'action' => 'getAllCurrencies']);
$router->get('/currencies/codes', ['controller' => 'App\Controller\CurrenciesController', 'action' => 'getCurrencyCodes']);
$router->post('/order', ['controller' => 'App\Controller\OrderController', 'action' => 'store']);
$router->post('/refresh-rates', ['controller' => 'App\Controller\CurrenciesController', 'action' => 'refreshRates']);
$router->get('/last-entry', ['controller' => 'App\Controller\OrderController', 'action' => 'getLastEntry']);


return $router;