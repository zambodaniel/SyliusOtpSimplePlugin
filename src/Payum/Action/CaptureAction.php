<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Api;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface, GatewayAwareInterface
{

    use ApiAwareTrait, GatewayAwareTrait, GenericTokenFactoryAwareTrait;

    /** @var Api $api */
    protected $api;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var TokenInterface $token */
        $token = $request->getToken();
        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();
        /** @var ArrayObject $details */
        $details = $request->getModel();
        if (empty($details['transactionId'])) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $token->getGatewayName(),
                $token->getDetails()
            );
            try {
                $response = $this->api->createPaymentRequest($payment, $token, $notifyToken);
                if (isset($response['errorCodes'])) {
                    $details['status'] = GetHumanStatus::STATUS_FAILED;
                    $details['errorCodes'] = $response['errorCodes'];
                    $payment->setDetails((array) $details);
                    return;
                }
                $details['orderRef'] = sprintf('%s|%s', $payment->getOrder()->getNumber(), $notifyToken->getHash());
                $details['salt'] = $response['salt'];
                $details['merchant'] = $response['merchant'];
                $details['transactionId'] = $response['transactionId'];
                $details['timeout'] = $response['timeout'];
                $details['paymentUrl'] = $response['paymentUrl'];
                $payment->setDetails((array) $details);
            } catch (\Throwable $exception) {
                $details['status'] = GetHumanStatus::STATUS_FAILED;
                $payment->setDetails((array) $details);
                return;
            }
            throw new HttpPostRedirect($details['paymentUrl']);
        } elseif (isset($_REQUEST['r'])) {
            $result = json_decode(base64_decode($_REQUEST['r']), true);
            if (is_array($result) && isset($result['e'])) {
                $details['status'] = $result['e'];
            }
        } else {
            if ($status = $this->api->getTransactionStatus($details['transactionId'])) {
                $details['status'] = $status;
            }
        }
        $payment->setDetails((array) $details);
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getFirstModel() instanceof SyliusPaymentInterface;
    }

}
