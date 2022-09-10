<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple;

final class SimplePayRefund extends Base
{
    protected $currentInterface = 'refund';
    protected $returnData = [];
    public $transactionBase = [
        'salt' => '',
        'merchant' => '',
        'orderRef' => '',
        'transactionId' => '',
        'currency' => '',
    ];

    /**
     * Run refund
     *
     * @return array $result API response
     */
    public function runRefund()
    {
        if ($this->transactionBase['orderRef'] == '') {
            unset($this->transactionBase['orderRef']);
        }
        if ($this->transactionBase['transactionId'] == '') {
            unset($this->transactionBase['transactionId']);
        }
        $this->logTransactionId = @$this->transactionBase['transactionId'];
        $this->logOrderRef = @$this->transactionBase['orderRef'];
        return $this->execApiCall();
    }
}
