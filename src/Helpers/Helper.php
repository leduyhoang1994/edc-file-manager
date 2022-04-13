<?php
namespace EdcCommon\ResourceManager\Helpers;

class Helper
{
    public static function removeUnicode($str)
    {
        // In thường
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        // In đậm
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str; // Trả về chuỗi đã chuyển
    }

    public static function changeToSlug($str)
    {
        $slug = self::removeUnicode($str);
        $slug = str_replace('_', '-', $slug);
        $slug = self::removeSpecialCharacter($slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);
        return preg_replace('/\-+/', '-', $slug);
    }

    public static function removeSpecialCharacter($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-\.]/', '', $string); // Removes special chars.
    }

    public static function getFileStatic($link): string
    {
        return config('app.static_url'). $link;
    }

    public static function makeUrl($baseUrl, ...$args)
    {
        return $baseUrl . self::makePath(...$args);
    }

    public static function makeResourceUrl($path, $root)
    {
        return self::makeUrl(config('cdn.domain'), $root, $path);
    }

    public static function makePath(...$args)
    {
        $path = '';

        foreach ($args as $arg) {
            $path .= '/' . $arg;
        }

        return preg_replace('~/+~', '/', $path);
    }

    /**
     * @param $size
     * @return void
     */
    public static function setUploadSize($size)
    {
        ini_set('post_max_size', $size);
        ini_set('upload_max_filesize', $size);
    }
}
