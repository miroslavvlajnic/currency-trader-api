<?php

namespace Core;

use Core\Db;

class BaseModel {

  protected $db;

  public function __construct() {
    $this->db = Db::getConnection();
  }

}