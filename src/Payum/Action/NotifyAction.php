<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Api;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Request\Notify;

class NotifyAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait, ApiAwareTrait;

    /**
     * @var Api
     */
    protected $api;

    protected ?Request $request = null;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Notify $request
     * @return void
     */
    public function execute($request)
    {
        if ($this->request === null) {
            $this->setRequest($request->getRequest());
        }
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var ArrayObject $details */
        $details = $request->getModel();
        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();
        /** @var TokenInterface $token */
        $token = $request->getToken();

        $result = $this->api->processIpn($this->request);
        if (null === $result) {
            throw new HttpResponse('', Response::HTTP_BAD_REQUEST);
        }

        $content = json_decode($result['confirmContent'], true);
        $details['status'] = $content['status'];
        $payment->setDetails((array) $details);

        throw new HttpResponse(
            $result['confirmContent'],
            Response::HTTP_OK,
            [
                'Accept-language' => 'EN',
                'Content-type' => 'application/json',
                'Signature' => $result['signature']
            ]
        );
    }

    /**
     * @param Notify $request
     * @return bool
     */
    public function supports($request): bool
    {
        return ($request instanceof Notify) && $request->getModel() instanceof ArrayObject;
    }
}
