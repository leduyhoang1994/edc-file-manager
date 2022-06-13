<?php

namespace EdcCommon\ResourceManager\Services\Upload\AWS;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use EdcCommon\ResourceManager\Exceptions\ResourceManagerException;
use EdcCommon\ResourceManager\Helpers\Helper;
use EdcCommon\ResourceManager\Models\Config;
use EdcCommon\ResourceManager\Models\UploadResource;
use EdcCommon\ResourceManager\Services\Upload\Contracts\UploadServiceAbstract;

class AwsService extends UploadServiceAbstract implements \EdcCommon\ResourceManager\Services\Upload\Contracts\UploadServiceInterface
{
    /** @var array */
    private $awsConfig;

    /** @var S3Client */
    private $client;

    /** @var array */
    private $credentials;

    /** @var string */
    private $bucket;

    /** @var string */
    private $key;

    /**
     * @param Config $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->awsConfig = $config->getAwsConfig();
        $credentials = [
            'key'    => Helper::arrGet($this->awsConfig, 'key', ''),
            'secret' => Helper::arrGet($this->awsConfig, 'secret', ''),
            'driver' => Helper::arrGet($this->awsConfig, 'driver', ''),
            'region' => Helper::arrGet($this->awsConfig, 'region', ''),
            'bucket' => Helper::arrGet($this->awsConfig, 'bucket', ''),
        ];

        $this->credentials = new Credentials($credentials['key'], $credentials['secret']);

        $this->bucket = Helper::arrGet($credentials, 'bucket');
        $this->key = Helper::arrGet($credentials, 'key');

        $this->client = new S3Client([
            'version'     => '2006-03-01',
            'region' => $credentials['region'],
            'credentials' => $this->credentials
        ]);
    }

    private function _getFilePath($fileName)
    {
        $root = $this->config->getRootFolder();

        if ($this->config->useDatePath()) {
            $pathByDate = date('Y') . '/' . date('m') . '/' . date('d') . "/";
            $root = Helper::makePath($root, $pathByDate);
        }

        $path = Helper::makePath($root, $fileName);

        return ltrim($path, '/');
    }

    private function _uploadIcon($uploadResource)
    {
        $uploadedFile = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->_getFilePath($uploadResource->getIconFileName()),
            'SourceFile' => $uploadResource->getIconFile()->getRealPath()
        ]);

        if (!$uploadedFile) {
            throw new ResourceManagerException('Upload Icon to S3 Bucket failed');
        }

        return $uploadedFile->get('ObjectURL');
    }

    /**
     * @inheritDoc
     * @throw ResourceManagerException
     */
    public function upload($uploadResource)
    {
        $uploadedFile = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->_getFilePath($uploadResource->getFileName()),
            'SourceFile' => $uploadResource->getFile()->getRealPath()
        ]);

        if (!$uploadedFile) {
            throw new ResourceManagerException('Upload to S3 Bucket failed');
        }

        $fileUrl = $uploadedFile->get('ObjectURL');
        $file = $uploadResource->getFile();
        $uploadResource->setFile($fileUrl);

        if ($uploadResource->getIconFile()) {
            if ($uploadResource->isSameFile($file)) {
                $uploadResource->setIconFile($fileUrl);
            } else {
                $uploadResource->setIconFile($this->_uploadIcon($uploadResource));
            }
        }

        return $uploadResource;
    }
}