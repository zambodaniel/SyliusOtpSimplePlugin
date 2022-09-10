<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple;

use function ZamboDaniel\SyliusOtpSimplePlugin\SDK\count;

final class SimplePayQuery extends Base
{
    protected $currentInterface = 'query';
    protected $returnData = [];
    protected $transactionBase = [
        'salt' => '',
        'merchant' => ''
    ];

    /**
     * Add SimplePay transaction ID to query
     *
     * @param string $simplePayId SimplePay transaction ID
     *
     * @return void
     */
    public function addSimplePayId($simplePayId = '')
    {
        if (!isset($this->transactionBase['transactionIds']) || count($this->transactionBase['transactionIds']) === 0) {
            $this->logTransactionId = $simplePayId;
        }
        $this->transactionBase['transactionIds'][] = $simplePayId;
    }

    /**
     * Add merchant order ID to query
     *
     * @param string $merchantOrderId Merchant order ID
     *
     * @return void
     */
    public function addMerchantOrderId($merchantOrderId = '')
    {
        if (!isset($this->transactionBase['orderRefs']) || count($this->transactionBase['orderRefs']) === 0) {
            $this->logOrderRef = $merchantOrderId;
        }
        $this->transactionBase['orderRefs'][] = $merchantOrderId;
    }

    /**
     * Run transaction data query
     *
     * @return array $result API response
     */
    public function runQuery()
    {
        return $this->execApiCall();
    }
}
