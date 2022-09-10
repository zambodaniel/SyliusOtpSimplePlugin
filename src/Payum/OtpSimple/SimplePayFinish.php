<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple;

final class SimplePayFinish extends Base
{
    protected $currentInterface = 'finish';
    protected $returnData = [];
    public $transactionBase = [
        'salt' => '',
        'merchant' => '',
        'orderRef' => '',
        'transactionId' => '',
        'originalTotal' => '',
        'approveTotal' => '',
        'currency' => '',
    ];

    /**
     * Run finish
     *
     * @return array $result API response
     */
    public function runFinish()
    {
        return $this->execApiCall();
    }
}
