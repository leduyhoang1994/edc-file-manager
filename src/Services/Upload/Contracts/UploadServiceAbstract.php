<?php

namespace EdcCommon\ResourceManager\Services\Upload\Contracts;

abstract class UploadServiceAbstract implements UploadServiceInterface
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
