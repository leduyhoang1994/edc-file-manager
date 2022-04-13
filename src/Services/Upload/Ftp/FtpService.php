<?php

namespace EdcCommon\ResourceManager\Services\Upload\Ftp;

use EdcCommon\ResourceManager\Exceptions\ResourceManagerException;
use EdcCommon\ResourceManager\Helpers\Helper;
use EdcCommon\ResourceManager\Models\UploadResource;
use EdcCommon\ResourceManager\ResourceManager;
use EdcCommon\ResourceManager\Services\Upload\Contracts\UploadServiceAbstract;

class FtpService extends UploadServiceAbstract
{
    protected $ftpConfig;

    protected $server;
    protected $port;
    protected $authUsername;
    protected $authPassword;
    protected $connection;

    const UPLOAD_ROOT = '/core_cms/resources/uploads/';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->ftpConfig = $config['ftp_upload'];

        $this->server = $this->ftpConfig['host'];
        $this->port = intval($this->ftpConfig['port']);
        $this->authUsername = $this->ftpConfig['username'];
        $this->authPassword = $this->ftpConfig['password'];
    }

    public function connect()
    {
        if (!$this->server || !$this->authUsername || !$this->authPassword) {
            throw new ResourceManagerException('server, authUsername, authPassword must be set.');
        }

        $this->connection = ftp_connect($this->server, $this->port);
        $loginResult = ftp_login($this->connection, $this->authUsername, $this->authPassword);

        if (!$loginResult) {
            throw new ResourceManagerException('Unable to connect to FTP Server ' . $this->server);
        }

        if (!ftp_pasv($this->connection, true)) {
            throw new ResourceManagerException("Cannot switch to passive mode");
        }

        return true;
    }

    public function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    private function closeConnection()
    {
        ftp_close($this->connection);
        $this->connection = null;
    }

    /**
     * @param $path
     * @return bool
     * @throws ResourceManagerException
     */
    public function mkdirRecursive($path)
    {
        $conn = $this->getConnection();

        $parts = explode("/",$path);
        $fullPath = "";
        foreach($parts as $part){
            if(empty($part)){
                $fullPath .= "/";
                continue;
            }
            $fullPath .= $part."/";
            if(@ftp_chdir($conn, $fullPath)){
                ftp_chdir($conn, $fullPath);
            }else{
                if(@ftp_mkdir($conn, $part)){
                    ftp_chdir($conn, $part);
                }else{
                    $this->closeConnection();
                    throw new ResourceManagerException('Unable to create directory');
                }
            }
        }

        $this->closeConnection();
        return true;
    }

    public function fileIsExist($path)
    {
        $conn = $this->getConnection();
        $size = ftp_size($conn, $path);
        $this->closeConnection();

        return $size > 0;
    }

    public function folderIsExist($path)
    {
        $conn = $this->getConnection();
        $list = ftp_nlist($conn, $path);
        $this->closeConnection();

        return $list;
    }

    private function getFolderByType($type)
    {
        $type = strtolower($type);
        if (in_array(strtoupper($type), ResourceManager::TYPE_LIST)) {
            return "${type}s/";
        }

        return 'others/';
    }

    private function generatePath($fileName, $type, $detail = false)
    {
        $pathByTime = date('Y') . '/' . date('m') . '/' . date('d') . "/";
        $relativePath = Helper::makePath(
            $this->getFolderByType($type),
            $pathByTime
        );

        $uploadPath = Helper::makePath(
            self::UPLOAD_ROOT,
            $relativePath
        );

        $fileNameSlug = Helper::changeToSlug($fileName);
        $fullPath = Helper::makePath($uploadPath, $fileNameSlug);

        if ($detail) {
            return compact('uploadPath', 'relativePath', 'fileNameSlug', 'fullPath');
        }

        return $fullPath;
    }

    private function addUniqueToFileName($fileName)
    {
        $exploded = explode('.', $fileName);
        $exploded[count($exploded) - 2] .= '-' . bin2hex(random_bytes(4));
        return implode('.', $exploded);
    }

    /**
     * @inheritDoc
     */
    public function upload($resource)
    {
        $resourcePath = $this->uploadResource($resource);
        $resource->setFilePath($resourcePath);

        if ($resource->getIconFile()) {
            if ($resource->isSameFile()) {
                $resource->setIconFilePath($resourcePath);
            } else {
                $resource->setIconFilePath($this->uploadIcon($resource));
            }
        }

        return $resource;
    }

    /**
     * @param UploadResource $resource
     * @return mixed
     */
    protected function uploadResource($resource)
    {
        $pathDetail = $this->generatePath($resource->getFileName(), $resource->getType(), true);

        /**
         * @var $uploadPath
         * @var $relativePath
         * @var $fileNameSlug
         * @var $fullPath
         */
        extract($pathDetail);

        $uploadedFileName = $this->storeFile($resource->getFile(), $uploadPath, $fileNameSlug);
        return Helper::makePath($relativePath, $uploadedFileName);
    }

    /**
     * @param UploadResource $resource
     * @return mixed
     */
    protected function uploadIcon($resource)
    {
        $pathDetail = $this->generatePath($resource->getIconFileName(), ResourceManager::TYPE_IMAGE, true);

        /**
         * @var $uploadPath
         * @var $relativePath
         * @var $fileNameSlug
         * @var $fullPath
         */
        extract($pathDetail);
        $uploadedFileName = $this->storeFile($resource->getIconFile(), $uploadPath, $fileNameSlug);
        return Helper::makePath($relativePath, $uploadedFileName);
    }

    /**
     * @param \SplFileObject $localFile
     * @param $path
     * @param $fileName
     * @throws
     * @return mixed|string
     */
    public function storeFile($localFile, $path, $fileName)
    {
        if (!$this->folderIsExist($path)) {
            $this->mkdirRecursive($path);
        }

        $fullPath = Helper::makePath($path, $fileName);
        if ($this->fileIsExist($fullPath)) {
            $fileName = $this->addUniqueToFileName($fileName);
            $fullPath = Helper::makePath($path, $fileName);
        }

        $conn = $this->getConnection();
        $uploadProgress = ftp_nb_put($conn, $fullPath, $localFile->getRealPath(), FTP_BINARY);

        while ($uploadProgress === FTP_MOREDATA) {
            $uploadProgress = ftp_nb_continue($conn);
        }

        if ($uploadProgress !== FTP_FINISHED) {
            $this->closeConnection();
            throw new ResourceManagerException('There was an error while uploading file');
        }

        $this->closeConnection();

        return $fileName;
    }
}
