<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Common;

use ZamboDaniel\SyliusOtpSimplePlugin\SDK\Common\Exception;

trait Signature
{
    /**
     * Get full JSON hash string form hash calculation base
     *
     * @param string $data Data array for checking
     *
     * @return void
     */
    public function getHashBase($data = '')
    {
        return $this->checkOrSetToJson($data);
    }

    /**
     * Gives HMAC signature based on key and hash string data
     *
     * @param string $key  Secret key
     * @param string $data Hash string
     *
     * @return string Signature
     */
    public function getSignature($key = '', $data = '')
    {
        if ($key == '' || $data == '') {
            $this->logContent['signatureGeneration'] = 'Empty key or data for signature';
        }
        return base64_encode(hash_hmac($this->hashAlgo, $data, trim($key), true));
    }

    /**
     * Check data based on signature
     *
     * @param string $data             Data for check
     * @param string $signatureToCheck Signature to check
     *
     * @return boolean
     */
    public function isCheckSignature($data = '', $signatureToCheck = '')
    {
        $this->config['computedSignature'] = $this->getSignature($this->config['merchantKey'], $data);
        $this->logContent['signatureToCheck'] = $signatureToCheck;
        $this->logContent['computedSignature'] = $this->config['computedSignature'];
        try {
            if ($this->phpVersion >= 7) {
                if (!hash_equals($this->config['computedSignature'], $signatureToCheck)) {
                    throw new \Exception('fail');
                }
            } elseif ($this->phpVersion < 7) {
                if ($this->config['computedSignature'] !== $signatureToCheck) {
                    throw new \Exception('fail');
                }
            }
        } catch (\Exception $e) {
            $this->logContent['hashCheckResult'] = $e->getMessage();
            return false;
        }
        $this->logContent['hashCheckResult'] = 'success';
        return true;
    }

    /**
     * Get signature value from header
     *
     * @param array $header Header
     *
     * @return string Signature
     */
    protected function getSignatureFromHeader($header = [])
    {
        $signature = 'MISSING_HEADER_SIGNATURE';
        foreach ($header as $headerKey => $headerValue) {
            if (strtolower($headerKey) === 'signature') {
                $signature = trim($headerValue);
            }
        }
        return $signature;
    }
}
