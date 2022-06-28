<?php

return [
    // Base API url for calling file service
    'base_url' => 'localhost',
    // Static domain for static resource
    'static_domain' => 'localhost',

    'upload_option' => [
        'root_folder' => '',
        'use_date_path' => true
    ],

    // File manager method
    'upload_method' => \EdcCommon\ResourceManager\ResourceManager::UPLOAD_METHOD_FTP,

    // Store method
    'store_method' => \EdcCommon\ResourceManager\ResourceManager::STORE_METHOD_API,

    // Ftp upload config
    'ftp_upload' => [
        'host' => 'localhost',
        'port' => 21,
        'domain'   => 'https://static.edupia.edu.vn',
        'username' => 'username',
        'password' => 'password'
    ],

    // Aws upload config
    'aws_upload' => [
        'driver' => 'xxxxxxx',
        'key'    => 'xxxxxxx',
        'secret' => 'xxxxxxx',
        'region' => 'xxxxxxx',
        'bucket' => 'xxxxxxx',
    ],

    // API store config
    'api_store' => [
        'host' => 'localhost',
        'secret_token' => 'xxx',
        'path' => 'api/file'
    ],
];
