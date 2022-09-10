<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Controller;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\RequestTokenVerifier;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Action\NotifyAction;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Api;
use ZamboDaniel\SyliusOtpSimplePlugin\Payum\Request\Notify;

class IpnController extends AbstractController
{

    private Payum $payum;
    private LoggerInterface $logger;

    public function __construct($service, LoggerInterface $logger)
    {
        $this->payum = $service;
        $this->logger = $logger;
    }

    public function process(Request $request)
    {
        $token = $this->verify($request);
        $gateway = $this->payum->getGateway($token->getGatewayName());
        $action = new Notify($token);
        $action->setRequest($request);
        $gateway->execute($action);
    }

    private function verify(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        if (false === $hash = isset($content['orderRef']) ? explode('|', $content['orderRef'])[1] : false) {
            throw new NotFoundHttpException('Token parameter not set in request');
        }

        if ($hash instanceof TokenInterface) {
            $token = $hash;
        } else {
            if (false == $token = $this->payum->getTokenStorage()->find($hash)) {
                throw new NotFoundHttpException(sprintf('A token with hash `%s` could not be found.', $hash));
            }
        }

        return $token;
    }
}
