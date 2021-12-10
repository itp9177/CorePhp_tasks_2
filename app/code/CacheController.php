<?php

namespace App\Code;

use Printful\Interfaces\CacheInterface;

class Cachecontroller implements CacheInterface
{

    const CACHE_PATH = __DIR__ . "/../../tmp/cache/";
    const KEYS_REGISTER_PATH = self::CACHE_PATH . '../CACHE_REGISTER.json';
    public static $controller;
    private static $keys_register = [];

    function __construct()
    {
        if (file_exists(self::KEYS_REGISTER_PATH)) {
            self::$keys_register = json_decode(file_get_contents(self::KEYS_REGISTER_PATH), true);
        }

    }

    /**
     * create singletone of CacheInterface
     */
    public static function call()
    {
        if (self::$controller == NULL)
            self::$controller = new Cachecontroller;

        return self::$controller;
    }

    function __destruct()
    {
        if (file_exists(self::KEYS_REGISTER_PATH)) {
            file_put_contents(self::KEYS_REGISTER_PATH, json_encode(self::$keys_register));
        }
    }

    public function set(string $key, $value, int $duration)
    {

        $fileName = $this->createFileName();
        $this->writeCache($value, $fileName);
        $this->setKey($key, $fileName, $duration);
        return;
    }

    private function createFileName()
    {
        return time() . uniqid(rand());
    }

    private function writeCache($value, $fileName)
    {
        $filePath = $this->getFilePath($fileName);
        file_put_contents($filePath, json_encode($value));
    }

    private function getFilePath($fileName)
    {
        return self::CACHE_PATH . $fileName . '.json';
    }

    private function setKey($key, $fileName, int $duration)
    {
        self::$keys_register[$key] = ['fileName' => $fileName, 'duration' => $duration];
    }

    public function get($key)
    {
        if ($this->cacheAvailable($key)) {
            $duration = self::$keys_register[$key]['duration'];
            $fileName = self::$keys_register[$key]['fileName'];
            $filePath = $this->getFilePath($fileName);
            $fileContent = [];

            if ((time() - filemtime($filePath)) < $duration) {

                $fileContent = json_decode(file_get_contents($filePath), true);

                return $fileContent;
            } else
                return NULL;
        }
        return NULL;
    }

    private function cacheAvailable($key)
    {

        if (self::$keys_register == null)
            return false;

        return array_key_exists($key, self::$keys_register) ? TRUE : FALSE;
    }
}