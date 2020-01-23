<?php

namespace App\Model;

use Core\BaseModel;

class Order extends BaseModel
{

    public function calculate($params)
    {
        $stmt = $this->db->query("SELECT * FROM currencies WHERE code = '{$params['currency']}'");
        $currency = $stmt->fetch(\PDO::FETCH_ASSOC);

        $calculatedAmount = $params['amount'] / $currency['exchange_rate'];
        $surcharge = $calculatedAmount * ($currency['surcharge_percentage'] / 100);
        $totalAmount = $calculatedAmount + $surcharge;

        return [
            'calculated_amount' => $calculatedAmount,
            'surcharge' => $surcharge,
            'total_amount' => $totalAmount
        ];
    }


    public function store($params)
    {
        $currency = new Currency;
        $currencyToStore = $currency->getByCode($params['currency']);

        $data = [
            'id' => $currencyToStore['id'],
            'exchange_rate' => $currencyToStore['exchange_rate'],
            'surcharge_percentage' => $currencyToStore['surcharge_percentage'],
            'surcharge_amount' => $params['surcharge'],
            'amount_purchased' => $params['amount'],
            'amount_payed' => $params['total_amount'],
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];

        try {
            $sql = "INSERT INTO orders (currency_id, exchange_rate, surcharge_percentage, surcharge_amount, amount_purchased, amount_payed, discount_percentage, discount_amount, created_at) 
                    VALUES (:id, :exchange_rate, :surcharge_percentage, :surcharge_amount, :amount_purchased, :amount_payed, :discount_percentage, :discount_amount, :created_at)";
            $this->db->prepare($sql)->execute($data);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
//        return var_dump($params);
//        die('String');
        $this->extraAction($params);

        return true;

    }

    public function getLastEntry() {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function extraAction($params)
    {
//        var_dump($params);
//        die;
        switch ($params['currency']) {
            case 'GBP':
                // send email
                $this->sendOrderDetailsEmail($params);
                break;
            case 'EUR':
                // Apply a 2% discount on the total order amount
                $data = [
                    'amount_payed' => (1 - (EUR_TRANSACTION_DISCOUNT / 100)) * $params['total_amount'],
                    'discount_percentage' => EUR_TRANSACTION_DISCOUNT,
                    'discount_amount' => (EUR_TRANSACTION_DISCOUNT / 100) * $params['total_amount']
                ];
                try {
                    $getIdSql = "SELECT id FROM orders ORDER BY id DESC LIMIT 1";
                    $q = $this->db->query($getIdSql);
                    $res = $q->fetch(\PDO::FETCH_ASSOC);
                    $id = $res['id'];
                    $sql = "UPDATE orders SET amount_payed = :amount_payed, discount_percentage = :discount_percentage, discount_amount = :discount_amount WHERE id = {$id}";
                    $this->db->prepare($sql)->execute($data);
                } catch (\Exception $e) {
                    return false;
                }
                break;
        }
    }

    private function sendOrderDetailsEmail($params)
    {
        $toEmail = ADMIN_EMAIL;
        $subject = 'Testing PHP Mail';
        $message = 'This mail is sent using the PHP mail function';
        $headers = 'From: noreply@company.com';
        mail($toEmail, $subject, $message, $headers);
    }


}