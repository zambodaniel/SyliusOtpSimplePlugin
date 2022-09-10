<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;

class ConvertAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Convert $request
     * @return void
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
        $divisor = 10 ** $currency->exp;

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $details['status'] = null;
        $details['orderRef'] = $payment->getNumber();
        $details['currency'] = $payment->getCurrencyCode();
        $details['total'] = $payment->getTotalAmount() / $divisor;
        $details['customerEmail'] = $payment->getClientEmail();
        $details['language'] = 'HU';
        $details['methods'] = ['CARD'];

        $request->setResult((array) $details);
    }

    public function supports($request)
    {
        return $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            'array' == $request->getTo()
            ;
    }
}
