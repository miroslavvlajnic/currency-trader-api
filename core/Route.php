<?php

namespace Core;

class Route {

  private $httpMethod;
  private $urlPattern;
  private $controller;
  private $action;

  public function __construct($httpMethod, $urlPattern, $handler) {
    $this->httpMethod = $httpMethod;
    $this->urlPattern = $urlPattern;
    $this->controller = $handler['controller'];
    $this->action = $handler['action'];
  }

  public function getHttpMethod() {
    return $this->httpMethod;
  }

  public function getUrlPattern() {
    return $this->urlPattern;
  }

  public function getController() {
    return $this->controller;
  }

  public function getAction() {
    return $this->action;
  }
}