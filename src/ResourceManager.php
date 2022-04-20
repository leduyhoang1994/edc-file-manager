<?php
namespace EdcCommon\ResourceManager;

use EdcCommon\ResourceManager\Exceptions\ResourceManagerException;
use EdcCommon\ResourceManager\Models\UploadResource;
use EdcCommon\ResourceManager\Services\Store\Api\ApiService;
use EdcCommon\ResourceManager\Services\Store\Contracts\StoreServiceInterface;
use EdcCommon\ResourceManager\Services\Upload\Ftp\FtpService;
use EdcCommon\ResourceManager\Services\Upload\Contracts\UploadServiceInterface;

class ResourceManager
{
    const TYPE_VIDEO = 'VIDEO';
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_AUDIO = 'AUDIO';
    const TYPE_VIDEO_TIMESTAMP = 'VIDEO_TIMESTAMP';
    const TYPE_PDF = 'PDF';

    const TYPE_LIST = [
        self::TYPE_VIDEO,
        self::TYPE_IMAGE,
        self::TYPE_AUDIO,
        self::TYPE_VIDEO_TIMESTAMP,
        self::TYPE_PDF
    ];

    const UPLOAD_METHOD_FTP = 'ftp';

    const UPLOAD_METHODS = [
        self::UPLOAD_METHOD_FTP,
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
        $config = $baseConfig = include __DIR__ . '/../config.php';
        $configPath = $this->getConfigPath();

        if ($configPath && is_string($configPath) && is_file($configPath)) {
            $newConfig = include $configPath;

            if (!is_array($newConfig)) {
                $newConfig = [];
            }

            $config = array_replace_recursive($baseConfig, $newConfig);
        }

        if ($configPath && is_array($configPath)) {
            $config = array_replace_recursive($baseConfig, $configPath);
        }

        $this->config = $config;

        $this->uploadService = $this->getUploadService();
        $this->storeService = $this->getStoreService();
    }

    /**
     * @return UploadServiceInterface
     */
    protected function getUploadService()
    {
        $config = $this->config;

        $method = $config['upload_method'];

        switch ($method) {
            case self::UPLOAD_METHOD_FTP :
                return new FtpService($config);
        }

        return new FtpService($config);
    }

    /**
     * @return StoreServiceInterface
     */
    protected function getStoreService()
    {
        $config = $this->config;

        $method = $config['store_method'];

        switch ($method) {
            case self::STORE_METHOD_API :
                return new ApiService($config);
        }

        return new ApiService($config);
    }

    public function setUploadMethod($method)
    {
        if (!in_array($method, self::UPLOAD_METHODS)) {
            throw new ResourceManagerException('Upload method not support');
        }

        $this->config['upload_method'] = $method;
        $this->uploadService = $this->getUploadService();
    }

    /**
     * @param UploadResource $uploadResource
     * @return UploadResource
     */
    public function store($uploadResource)
    {
        $uploadResource->validateCreate();

//        $uploadResource = $this->uploadService->upload($uploadResource);
        $uploadResource = $this->storeService->store($uploadResource);

        return $uploadResource;
    }
}
