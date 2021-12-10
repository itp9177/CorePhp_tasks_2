<?php

namespace App\Code;

use App\Code\CacheController;
use App\Code\ShippingApi;

class FetchData
{

    private const DURATION = 300;
    private $cacheController;
    private $shippingApi;

    function __construct()
    {
        $this->cacheController = CacheController::Call();
        $this->shippingApi = new ShippingApi;
    }

    public function fetchShippingRates($key)
    {
        $result = $this->cacheController->get($key);
        
        if (!isset($result)) {
            $result = $this->shippingApi->getRates($key);
            $this->cacheController->set($key, $result, self::DURATION);
        }

        return $result;

    }
}