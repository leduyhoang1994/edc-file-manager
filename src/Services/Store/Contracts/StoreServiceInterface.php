<?php

namespace EdcCommon\ResourceManager\Services\Store\Contracts;

use EdcCommon\ResourceManager\Models\UploadResource;

interface StoreServiceInterface
{
    /**
     * @param UploadResource $uploadResource
     * @return UploadResource
     */
    public function store($uploadResource);
}
