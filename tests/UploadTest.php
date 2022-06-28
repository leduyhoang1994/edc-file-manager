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
            'upload_method' => \EdcCommon\ResourceManager\ResourceManager::UPLOAD_METHOD_FTP,
            'upload_option' => [
                'root_folder' => 'test/miis/abc',
                'use_date_path' => false
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
                'host' => 'https://cms-core.edupia.com.vn',
                'secret_token' => '556db0bdd55fdb5079b0b645ae890e00847490fa16141e96a83ee12c8fe438f4'
            ],
        ];
        $file = new SplFileObject($this->join_paths(__DIR__, 'image.jpg'));
        $fileName = 'test';
        $type = ResourceManager::TYPE_IMAGE;
        $iconFile = new SplFileObject($this->join_paths(__DIR__, 'test.tmp'));
        $iconFileName = 'icon';

        $resource = new UploadResource($file, $fileName, $type, $iconFile, $iconFileName);

        $manager = new ResourceManager($config);

        $result = $manager->store($resource);

//        var_dump($result);

        $this->assertIsArray($result);
    }
}