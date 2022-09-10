<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

final class StatusAction implements ActionInterface
{
    /**
     * @param GetStatusInterface $request
     * @return void
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();

        $details = $payment->getDetails();
        $status = $details['status'] ?? null;
        switch($status) {
            case 'INIT':
            case null:
                $request->markNew();
                break;
            case 'INPAYMENT':
                $request->markPending();
            case 'INFRAUD':
                $request->markSuspended();
            case 'AUTHORIZED':
                $request->markAuthorized();
            case 'FINISHED':
            case 'SUCCESS':
                $request->markCaptured();
                break;
            case 'FRAUD':
            case 'NOTAUTHORIZED':
            case 'FAIL':
                $request->markFailed();
                break;
            case 'REVERSED':
            case 'CANCELLED':
            case 'CANCEL':
                $request->markCanceled();
                break;
            case 'TIMEOUT':
                $request->markExpired();
                break;
            case 'REFOUND':
                $request->markRefunded();
                break;
            default:
                $request->markUnknown();

        }
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof ArrayObject;
    }

}
