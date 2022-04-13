<?php

namespace EdcCommon\ResourceManager\Services\Upload\Contracts;

use EdcCommon\ResourceManager\Models\UploadResource;

interface UploadServiceInterface
{
    /**
     * @param UploadResource $uploadResource
     * @return UploadResource
     */
    public function upload($uploadResource);
}
