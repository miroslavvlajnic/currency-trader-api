<?php

namespace App\Controller;

use Core\BaseController;
use App\Model\Currency;

class CurrenciesController extends BaseController
{

    private $currency;

    public function __construct()
    {
        $this->currency = new Currency;
    }

    public function getAllCurrencies()
    {
        $allCurrencies = $this->currency->getAll();
        return $this->jsonResponse($allCurrencies, 200);
    }

    public function getCurrencyCodes()
    {
        $currencyCodes = $this->currency->getCodes();
        return $this->jsonResponse($currencyCodes, 200);
    }

    public function refreshRates($latestRates)
    {
      $this->currency->refreshRates($latestRates);

    }

}