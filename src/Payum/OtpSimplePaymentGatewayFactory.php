<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action\CaptureAction;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action\ConvertAction;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action\NotifyAction;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action\StatusAction;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Api;

final class OtpSimplePaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'otp_simple_payment',
            'payum.factory_title' => 'Otp SimplePay Payment',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert' => new ConvertAction(),

        ]);
        $config['payum.api'] = function (ArrayObject $config) {
            return new Api(
                $config['merchant_id'],
                $config['secret_key'],
                $config['auto_challenge'],
                $config['log_enabled'],
                $config['sandbox']
            );
        };
    }
}
