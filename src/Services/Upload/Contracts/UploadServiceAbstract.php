<?php

namespace EdcCommon\ResourceManager\Services\Upload\Contracts;

use EdcCommon\ResourceManager\Models\Config;

abstract class UploadServiceAbstract implements UploadServiceInterface
{
    /** @var Config */
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
