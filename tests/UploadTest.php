<?php
use EdcCommon\ResourceManager\ResourceManager;
use EdcCommon\ResourceManager\Models\UploadResource;

class UploadTest extends \PHPUnit\Framework\TestCase
{

    function join_paths() {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') { $paths[] = $arg; }
        }

        return preg_replace('#/+#','/',join('/', $paths));
    }

    public function test()
    {
        $config = [
            'url' => '',
            'token' => '',
            'static_domain' => 'https://static.edupia.edu.vn',
            'upload_method' => \EdcCommon\ResourceManager\ResourceManager::UPLOAD_METHOD_AWS,
            'upload_option' => [
                'root_folder' => 'test/miis/abc',
                'use_date_path' => true
            ],
            'ftp_upload' => [
                'host'     => '13.251.192.209',
                'domain'   => 'https://static.edupia.edu.vn',
                'port'     => 21,
                'username' => 'sg-resource',
                'password' => 'yJP).7=v'
            ],
            'aws_upload' => [
                'driver' => 'xxxxxxx',
                'key'    => 'xxxxxxx',
                'secret' => 'xxxxxxx',
                'region' => 'xxxxxxx',
                'bucket' => 'xxxxxxx',
            ],
            'api_store' => [
                'host' => 'https://core-cms.dev',
                'secret_token' => '71ad4a6a4e9f875a07608b80327757f705b5e2fc5ad215ca6c1719736a47b59a'
            ],
        ];
        $file = new SplFileObject($this->join_paths(__DIR__, 'image.jpg'));
        $fileName = 'test.jpg';
        $type = ResourceManager::TYPE_IMAGE;
        $iconFile = new SplFileObject($this->join_paths(__DIR__, 'test.tmp'));
        $iconFileName = 'icon.jpg';

        $resource = new UploadResource($file, $fileName, $type, $iconFile, $iconFileName);

        $manager = new ResourceManager($config);

        $result = $manager->store($resource);

        $this->assertIsArray($result);
    }
}