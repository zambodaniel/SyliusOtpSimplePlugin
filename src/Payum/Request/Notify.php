<?php

namespace ZamboDaniel\SyliusOtpSimplePlugin\Payum\Request;

use Payum\Core\Request\Notify as BaseNotify;
use Symfony\Component\HttpFoundation\Request;

class Notify extends BaseNotify
{

    protected ?Request $request = null;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
