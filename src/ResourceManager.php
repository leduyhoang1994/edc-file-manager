<?php
namespace EdcCommon\ResourceManager;

use EdcCommon\ResourceManager\Exceptions\ResourceManagerException;
use EdcCommon\ResourceManager\Models\Config;
use EdcCommon\ResourceManager\Models\UploadResource;
use EdcCommon\ResourceManager\Services\Store\Api\ApiService;
use EdcCommon\ResourceManager\Services\Store\Contracts\StoreServiceInterface;
use EdcCommon\ResourceManager\Services\Upload\AWS\AwsService;
use EdcCommon\ResourceManager\Services\Upload\Ftp\FtpService;
use EdcCommon\ResourceManager\Services\Upload\Contracts\UploadServiceInterface;

class ResourceManager
{
    const TYPE_VIDEO = 'VIDEO';
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_AUDIO = 'AUDIO';
    const TYPE_VIDEO_TIMESTAMP = 'VIDEO_TIMESTAMP';
    const TYPE_PDF = 'PDF';
    const TYPE_ASSET_BUNDLE = 'ASSET_BUNDLE';

    const TYPE_LIST = [
        self::TYPE_VIDEO,
        self::TYPE_IMAGE,
        self::TYPE_AUDIO,
        self::TYPE_VIDEO_TIMESTAMP,
        self::TYPE_PDF,
        self::TYPE_ASSET_BUNDLE
    ];

    const UPLOAD_METHOD_FTP = 'ftp';
    const UPLOAD_METHOD_AWS = 'aws';
    const UPLOAD_METHOD_API = 'api';

    const UPLOAD_METHODS = [
        self::UPLOAD_METHOD_FTP,
        self::UPLOAD_METHOD_AWS,
        self::UPLOAD_METHOD_API,
    ];

    const STORE_METHOD_API = 'api';

    const STORE_METHODS = [
        self::STORE_METHOD_API,
    ];

    /** @var UploadServiceInterface */
    protected $uploadService;
    /** @var StoreServiceInterface */
    protected $storeService;

    protected $configPath;
    /** @var Config */
    protected $config;

    /**
     * @return mixed|null
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @param mixed|null $configPath
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
    }

    public function __construct($configPath = null)
    {
        $this->configPath = $configPath;
        $this->boot();
    }

    public function boot()
    {
        $this->config = new Config($this->getConfigPath());

        $this->uploadService = $this->getUploadService();
        $this->storeService = $this->getStoreService();
    }

    /**
     * @return UploadServiceInterface
     */
    public function getUploadService()
    {
        $config = $this->config;

        $method = $config->getUploadMethod();

        switch ($method) {
            case self::UPLOAD_METHOD_FTP :
                return new FtpService($config);
            case self::UPLOAD_METHOD_AWS :
                return new AwsService($config);
            case self::UPLOAD_METHOD_API :
                return null;
        }

        return new FtpService($config);
    }

    /**
     * @return StoreServiceInterface
     */
    public function getStoreService()
    {
        $config = $this->config;

        $method = $config->getStoreMethod();

        switch ($method) {
            case self::STORE_METHOD_API :
                return new ApiService($config);
        }

        return new ApiService($config);
    }

    /**
     * @param UploadResource $uploadResource
     * @return UploadResource
     */
    public function store($uploadResource)
    {
        $uploadResource->validateCreate();

        if ($this->uploadService) {
            $uploadResource = $this->uploadService->upload($uploadResource);
        }
        $uploadResource = $this->storeService->store($uploadResource);

        return $uploadResource;
    }
}
