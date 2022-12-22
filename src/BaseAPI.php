<?php

namespace Tnhnclskn\PayTR;

class BaseAPI
{
    private PayTR $paytr;

    public function __construct(PayTR $paytr)
    {
        $this->paytr = $paytr;
    }

    protected function paytr(): PayTR
    {
        return $this->paytr;
    }
}
