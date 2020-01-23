<?php

namespace App\Controller;

use Core\BaseController;
use App\Model\Order;

class OrderController extends BaseController {

  private $order;

  public function __construct() {
    $this->order = new Order;
  }

  public function calculate($params) {
    $data = $this->order->calculate($params);
    return $this->jsonResponse($data, 200);
  }

    public function store($params) {
        $data = $this->order->store($params);
        return $this->jsonResponse($data, 200);
    }

    public function getLastEntry() {
      $lastEntry = $this->order->getLastEntry();
        return $this->jsonResponse($lastEntry, 200);
    }

}