<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple;

use Payum\Core\Security\TokenInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use ZamboDaniel\SyliusOtpSimplePlugin\Exception\InvalidSimplepayRequestException;

final class Api
{

    private string $merchant_id;
    private string $secret_key;
    private bool $auto_challenge;
    private bool $log_enabled;
    private bool $sandbox;
    private ?LoggerInterface $logger;

    public function __construct(string $merchant_id, string $secret_key, bool $auto_challenge, bool $log_enabled, bool $sandbox, ?LoggerInterface $logger = null)
    {
        $this->merchant_id = $merchant_id;
        $this->secret_key = $secret_key;
        $this->auto_challenge = $auto_challenge;
        $this->log_enabled = $log_enabled;
        $this->sandbox = $sandbox;
        $this->logger = $logger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getTransactionStatus(string $transactionId): ?string
    {
        $trx = new SimplePayQuery();
        $trx->addConfig($this->getConfig());
        $trx->addSimplePayId($transactionId);
        /** @var array $result */
        $result = $trx->runQuery();
        foreach ($result['transactions'] as $item) {
            if ($item['transactionId'] === $transactionId) {
                return $item['status'];
            }
        }
        return null;
    }

    public function createPaymentRequest(SyliusPaymentInterface $payment, TokenInterface $token, TokenInterface $notifyToken): array
    {
        $order = $payment->getOrder();
        $address = $order->getBillingAddress();
        $trx = new SimplePayStart();
        $trx->setLogger($this->logger);
        $trx->addConfig($this->getConfig());
        $trx->addData('currency', $payment->getCurrencyCode());
        $trx->addData('orderRef', sprintf('%s|%s', $order->getNumber(), $notifyToken->getHash()));
        $trx->addData('methods', ['CARD']);
        $trx->addData('total', (int) ($payment->getAmount() / 100));
        $trx->addData('customerEmail', $order->getCustomer()->getEmail());
        $trx->addData('language', 'HU');
        $trx->addData('timeout', (new \DateTime('now + 6 minutes'))->format('c'));
        $trx->addData('url', $token->getTargetUrl());

        $trx->addData('phone', $address->getPhoneNumber());
        $invoice = [
            'name' => $address->getFullName(),
            'country' => $address->getCountryCode(),
            'state' => $address->getProvinceName(),
            'city' => $address->getCity(),
            'zip' => $address->getPostcode(),
            'address' => $address->getStreet(),
        ];
        $trx->addData('invoice', $invoice);

        $threeDSReqAuthMethod = $order->getCustomer()->hasUser() ? '02' : '01';


        $trx->runStart();

        $responseData = $trx->getReturnData();
        if ($responseData && array_key_exists('errorCodes', $responseData)) {
            throw new InvalidSimplepayRequestException(
                sprintf(
                    'Probably the request is missing a required param. Please check the documentation for the following error codes: %s',
                    implode(', ', $responseData['errorCodes'])
                )
            );
        }

        if (!$responseData['responseSignatureValid']) {
            throw new InvalidSignatureException('Response missing or invalid');
        }

        return $responseData;
    }

    public function processIpn(Request $request): ?array
    {
        $trx = new SimplePayIpn();
        $trx->addConfig($this->getConfig());
        if ($trx->isIpnSignatureCheck($request->getContent())) {
            return $trx->getIpnConfirmContent();
        }
        return null;
    }

    private function getConfig(): array
    {
        return [
            'HUF_MERCHANT' => $this->merchant_id,
            'HUF_SECRET_KEY' => $this->secret_key,
            'SANDBOX' => $this->sandbox,
            'GET_DATA' => (isset($_GET['r']) && isset($_GET['s'])) ? ['r' => $_GET['r'], 's' => $_GET['s']] : [],
            'POST_DATA' => $_POST,
            'SERVER_DATA' => $_SERVER,
            'LOGGER' => $this->log_enabled,
            'LOG_PATH' => 'log',
            'AUTOCHALLENGE' => $this->auto_challenge,
        ];
    }
}
