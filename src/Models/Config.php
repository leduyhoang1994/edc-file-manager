<?php

namespace EdcCommon\ResourceManager\Models;

use EdcCommon\ResourceManager\Helpers\Helper;

class Config
{
    /** @var array */
    private $config;

    public function __construct($configPath)
    {
        $this->config = $baseConfig = include __DIR__ . '/../../config.php';

        if ($configPath && is_string($configPath) && is_file($configPath)) {
            $newConfig = include $configPath;

            if (!is_array($newConfig)) {
                $newConfig = [];
            }

            $this->config = array_replace_recursive($baseConfig, $newConfig);
        }

        if ($configPath && is_array($configPath)) {
            $this->config = array_replace_recursive($baseConfig, $configPath);
        }
    }

    public function getAwsConfig()
    {
        return Helper::arrGet($this->config, 'aws_upload');
    }

    public function getFtpConfig()
    {
        return Helper::arrGet($this->config, 'ftp_upload');
    }

    public function getStoreMethod()
    {
        return Helper::arrGet($this->config, 'store_method');
    }

    public function getUploadMethod()
    {
        return Helper::arrGet($this->config, 'upload_method');
    }

    public function setUploadMethod($method)
    {
        Helper::arrSet($this->config, 'upload_method', $method);
    }

    public function getApiStore()
    {
        return Helper::arrGet($this->config, 'api_store', []);
    }

    public function getRootFolder()
    {
        return Helper::arrGet($this->config, 'upload_option.root_folder');
    }

    public function useDatePath()
    {
        return Helper::arrGet($this->config, 'upload_option.root_folder');
    }

    public function get($key)
    {
        return Helper::arrGet($this->config, $key);
    }
}