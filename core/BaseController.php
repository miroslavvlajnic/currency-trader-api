<?php

namespace Core;

class BaseController {

  public function jsonResponse($data, $responseCode) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin:*');
    http_response_code($responseCode);
    echo json_encode($data);
  }
}