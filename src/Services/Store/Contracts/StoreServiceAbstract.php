<?php

namespace EdcCommon\ResourceManager\Services\Store\Contracts;

use EdcCommon\ResourceManager\Models\Config;

abstract class StoreServiceAbstract implements StoreServiceInterface
{
    /** @var Config */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }
}
