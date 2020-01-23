<?php

namespace App\Model;

use Core\BaseModel;

class Currency extends BaseModel {

  public function getAll() {
    $stmt = $this->db->query('SELECT * FROM currencies');
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getCodes() {
    $stmt = $this->db->query('SELECT code FROM currencies');
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
  }

    public function getByCode($codeName) {
        $stmt = $this->db->query("SELECT * FROM currencies WHERE code='{$codeName}'");
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function refreshRates($latestRates) {
      $this->db->query("UPDATE currencies SET exchange_rate = {$latestRates['USDJPY']} WHERE id = 1");
      $this->db->query("UPDATE currencies SET exchange_rate = {$latestRates['USDGBP']} WHERE id = 2");
      $this->db->query("UPDATE currencies SET exchange_rate = {$latestRates['USDEUR']} WHERE id = 3");
    }

}