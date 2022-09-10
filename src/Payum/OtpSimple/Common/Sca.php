<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\OtpSimple\Common;

trait Sca
{
    /**
     * StartChallenge
     *
     * @param array $v2Result Result of API call
     *
     * @return boolean        Success of redirection
     */
    public function challenge($v2Result = [])
    {
        if (isset($v2Result['redirectUrl'])) {
            $this->returnData['paymentUrl'] = $v2Result['redirectUrl'];
            $this->getHtmlForm();
            $this->writeLog(['3DSCheckResult' => 'Card issuer bank wants to identify cardholder (challenge)', '3DSChallengeUrl' => $v2Result['redirectUrl']]);
            print $this->returnData['form'];
            return true;
        }
        $this->writeLog(['3DSCheckResult' => 'Card issuer bank wants to identify cardholder (challenge)', '3DSChallengeUrl_ERROR' => 'Missing redirect URL']);
        return false;
    }
}
