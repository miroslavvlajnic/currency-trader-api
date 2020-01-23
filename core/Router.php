<?php

namespace Core;

class Router {

  private $routeList = [];
  private $params;
  private $matchedRoutes = [];


  public function get($url, $params) {
    $this->addRoute('GET', $url, $params);
  }

  public function post($url, $params) {
    $this->addRoute('POST', $url, $params);
  }

  public function addRoute($method, $url, $params) {
    array_push($this->routeList, new Route($method, $url, $params));
  }

  private function setParams($key, $value) {
    $this->params[$key] = $value;
  }


  public function run($url, $httpMethod) {
    $routesMatchedByHttpMethod = $this->matchRouteByHttpMethod($httpMethod);
    $this->matchRouteByUrlPattern($url, $routesMatchedByHttpMethod);

    if (empty($this->matchedRoutes)) {
      // @TODO zameniti sa Exception-om
      die('route not found');
    }

    $this->dispatch();
  }

  private function matchRouteByHttpMethod($httpMethod) {
    $routesMatchedByHttpMethod = [];
    
    foreach ($this->routeList as $route) {
      if (strtoupper($httpMethod) == $route->getHttpMethod()) {
        array_push($routesMatchedByHttpMethod, $route);
      }
    }

    return $routesMatchedByHttpMethod;
  }

  private function matchRouteByUrlPattern($url, $routesMatchedByHttpMethod) {
    foreach ($routesMatchedByHttpMethod as $route) {
      if ($this->findRegexMatch($this->cleanUrl($url), $route->getUrlPattern())) {
        array_push($this->matchedRoutes, $route);
      }
    }
  }

  /**
   * $url - url of the current request (cleaned $_SERVER['REQUEST_URI'])
   * $pattern - url pattern saved in the Route object
   */
  private function findRegexMatch($url, $pattern) {
    // find all params denoted with ':' in route pattern and save them to $params var
    preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);
    
    $patternAsRegex = preg_replace_callback('@:([\w]+)@', [$this, 'convertPatternToRegex'], $pattern);
    $patternAsRegex = '@^' . $patternAsRegex . '$@';

    // if our url from the current request matches the pattern in the Route object url
    // we will return true and keep track of all the ':' parameters and their values
    if (preg_match($patternAsRegex, $url, $paramsValue)) {
      // we must remove the first item from the array cause it's the match of the full url
      // and we only need the matched parameters
      array_shift($paramsValue);
      $this->prepareParamsForExecution($params, $paramsValue);
      $this->preparePostParams();
      $this->prepareGetParams();

      return true;
    }

    return false;
  }

  private function prepareParamsForExecution($params, $paramValues) {
    foreach ($params[0] as $param) {
      // remove ':' from the $param
      $paramName = substr($param, 1);
      if ($paramValues[$paramName]) {
          $this->setParams($paramName, urlencode($paramValues[$paramName]));
      }
    }
  }

  private function preparePostParams() {
    if (!empty($_POST)) {
      foreach ($_POST as $paramName => $paramValue) {
        $this->setParams($paramName, $paramValue);
      }
    }
  }

  private function prepareGetParams() {
    if (!empty($_GET)) {
      foreach ($_GET as $paramName => $paramValue) {
        $this->setParams($paramName, $paramValue);
      }
    }
  }

  /**
   * Initializes the correct controller,
   * executes the method on the controller with
   * the params from the url
   */
  private function dispatch() {
    $controller = $this->matchedRoutes[0]->getController();
    $action = $this->matchedRoutes[0]->getAction();

    $ctrlObject = new $controller;

    if (is_callable([$ctrlObject, $action])) {
      call_user_func([$ctrlObject, $action], $this->params);
    } else {
      // @TODO zameniti sa Exception-om
      die('Method in Controller not callable!');
    }
  }


  private function convertPatternToRegex($matches) {
    $key = str_replace(':', '', $matches[0]);
    return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
  }

  function cleanURL($url) {
    return str_replace(['%20', ' '], '-', $url);
  }

}