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

        ];
        $file = new SplFileObject($this->join_paths(__DIR__, 'image.jpg'));
        $fileName = 'test.jpg';
        $type = ResourceManager::TYPE_IMAGE;
        $iconFile = new SplFileObject($this->join_paths(__DIR__, 'image.jpg'));
        $iconFileName = 'icon.jpg';

        $resource = new UploadResource($file, $fileName, $type, $iconFile, $iconFileName);

        $manager = new ResourceManager($config);

        $result = $manager->store($resource);

        var_dump($result);
    }
}