<?php

namespace EdcCommon\ResourceManager\Models;

use EdcCommon\ResourceManager\Exceptions\ResourceValidationException;
use EdcCommon\ResourceManager\ResourceManager;

class UploadResource
{
    protected $id;
    protected $name;
    protected $type;

    /** @var \SplFileObject|string */
    protected $file;
    protected $fileName;
    protected $fileType;
    /** @var \SplFileObject|string */
    protected $iconFile;
    protected $iconFileName;
    protected $iconFileType;

    protected $filePath;
    protected $iconFilePath;
    protected $description;
    protected $isActive = true;
    protected $num_repeat = 0;

    protected $requireds = [
        'name', 'type', 'file'
    ];

    /**
     * @param \SplFileObject|string $file
     * @param $name
     * @param $type
     * @param \SplFileObject|null|string $iconFile
     */
    public function __construct($file, $name, $type, $iconFile = null, $iconFileName = '')
    {
        $this->name = $name;
        $this->fileName = $name;
        $this->file = $file;
        $this->type = $type;
        $this->fileType = $type;

        $this->iconFile = $iconFile;
        $this->iconFileName = $iconFileName;
        $this->iconFileType = ResourceManager::TYPE_IMAGE;

//        $this->loadFile($file, 'file');
//
//        if ($iconFile) {
//            $this->loadFile($iconFile, 'iconFile');
//        }
    }

    protected function defaultValidate()
    {
        // Check required
        foreach ($this->requireds as $required) {
            if (!$this->$required) {
                throw new ResourceValidationException($required . ' is required field.');
            }
        }

        // Check file type
        $this->validateFileType('file', $this->type);
        $this->validateFileType('iconFile', ResourceManager::TYPE_IMAGE);
    }

    protected function validateFileType($field, $type = null)
    {
        $fieldName = $field.'Name';
        $fieldType = $field.'Type';

        $type = strtolower($type);
        $fileType = $this->$fieldType;

        return substr( $fieldType, 0, 4 ) === $type . '/';
    }

    public function validateCreate()
    {
        $this->defaultValidate();
    }

    /**
     * Check if resource file and icon file are the same
     *
     * @param \SplFileObject $file
     *
     * @return boolean
     */
    public function isSameFile($file = null)
    {
        if (!$this->getIconFile()) {
            return false;
        }

        $resourceFile = $file ? $file->getRealPath() : $this->getFile()->getRealPath();
        $iconUpload = $this->getIconFile()->getRealPath();

        return filesize($resourceFile) == filesize($iconUpload) && md5_file($resourceFile) == md5_file($iconUpload);
    }

    public function toArray()
    {
        $file = $this->file;

        if ($this->file instanceof \SplFileObject) {
            $file = curl_file_create($this->file->getRealPath(), '', $this->name);
        }

        $fileIcon = $this->iconFile;

        if ($this->iconFile instanceof \SplFileObject) {
            $fileIcon = curl_file_create($this->iconFile->getRealPath(), '', $this->iconFileName);
        }

        return [
            'name' => $this->name,
            'icon_file_name' => $this->iconFileName,
            'type' => $this->type,
            'file_path' => $file,
            'icon_file_path' => $fileIcon,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'num_repeat' => $this->num_repeat
        ];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \SplFileObject|string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($name)
    {
        $this->fileName = $name;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @return \SplFileObject|string
     */
    public function getIconFile()
    {
        return $this->iconFile;
    }

    /**
     * @return mixed
     */
    public function getIconFileName()
    {
        return $this->iconFileName;
    }

    /**
     * @return mixed
     */
    public function getIconFileType()
    {
        return $this->iconFileType;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return mixed
     */
    public function getIconFilePath()
    {
        return $this->iconFilePath;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param \SplFileObject|string $file
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param \SplFileObject|string $file
     * @return void
     */
    public function setIconFile($file)
    {
        $this->iconFile = $file;
    }

    /**
     * @param mixed $iconFilePath
     */
    public function setIconFilePath($iconFilePath)
    {
        $this->iconFilePath = $iconFilePath;
    }
}
