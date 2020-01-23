<?php

namespace Core;

class Db {

  protected static $db;

  private function __construct() {
    try {
      self::$db = new \PDO("mysql:host=" . DB['HOST'] . ";dbname=" . DB['NAME'], DB['USER'], DB['PASS']);
      self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    catch (\PDOException $e) {
      echo "Connection Error: " . $e->getMessage();
    }
  }

  public static function getConnection() {
    //Guarantees single instance, if no connection object exists then create one.
    if (!self::$db) {
      new Db();
    }
    return self::$db;
  }

}