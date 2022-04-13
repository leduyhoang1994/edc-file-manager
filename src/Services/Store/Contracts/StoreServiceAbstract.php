<?php

namespace EdcCommon\ResourceManager\Services\Store\Contracts;

abstract class StoreServiceAbstract implements StoreServiceInterface
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    protected function getConfig()
    {
        return $this->config;
    }
}
