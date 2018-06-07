<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Helper;

use StephenHill\Base58;

/**
 * 数据处理类
 */
class Data
{
    /**
     * 实例数组
     * @var array
     */
    static protected $instances = [];

    /**
     * 数据加解密私钥
     * @var string
     */
    const CRYPT_PRIVATE_KEY = 'ycJA1E5zdL93jYsSGXH04wdGNZ7eHoCr';

    /**
     * 数据加解密向量
     * @var string
     */
    const CRYPT_IV = '7QYgofxP/f85a9bbjus1ZQ==';

    /**
     * 获取一个实例
     * @param  string $className 需要获取实例的对象
     * @return object
     */
    public static function getInstance($className)
    {
        if (!isset(static::$instances[$className])) {
            $instance = new $className();
            static::$instances[$className] = $instance;
        }
        return static::$instances[$className];
    }

    /**
     * 对数据进行 Base58 加密
     * @param  string $data 待加密数据
     * @return string
     */
    public static function base58encode($data)
    {
        return static::getInstance(Base58::class)->encode($data);
    }

    /**
     * 对数据进行 Base58 解密
     * @param  string $data 带解密数据
     * @return string
     */
    public static function base58decode($data)
    {
        return static::getInstance(Base58::class)->decode($data);
    }

    /**
     * 通过 OpenSSL 加密数据
     * 这里使用了 AES-256-CBC 的方式对数据进行加密
     * @param  string $data       待加密数据内容
     * @param  string $privateKey 数据加解密私钥
     * @param  string $iv         经过 base64_encode 的加解密向量
     * @return string
     */
    public static function encrypt($data, $privateKey = null, $iv = null)
    {
        $privateKey = $privateKey ?: self::CRYPT_PRIVATE_KEY;
        $iv = $iv ?: self::CRYPT_IV;
        
        return base64_encode(
            openssl_encrypt($data, 'AES-256-CBC', $privateKey, OPENSSL_RAW_DATA, base64_decode($iv))
        );
    }

    /**
     * 通过 OpenSSL 解密数据
     * 这里使用了 AES-256-CBC 的方式对数据进行解密
     * @param  string $data       待加密数据内容
     * @param  string $privateKey 数据加解密私钥
     * @param  string $iv         经过 base64_encode 的加解密向量
     * @return string
     */
    public static function decrypt($data, $privateKey = null, $iv = null)
    {
        $privateKey = $privateKey ?: self::CRYPT_PRIVATE_KEY;
        $iv = $iv ?: self::CRYPT_IV;

        return openssl_decrypt(
            base64_decode($data), 'AES-256-CBC', $privateKey, OPENSSL_RAW_DATA, base64_decode($iv)
        );
    }

    /**
     * 私钥加密数据
     * @param  string $data       待加密数据
     * @param  mixed  $privateKey 私钥数据
     * @return string|false
     */
    public static function privateEncode($data, $privateKey)
    {
        $res = openssl_private_encrypt($data, $response, $privateKey);
        return $res ? self::base58encode($response) : false;
    }

    /**
     * 私钥解密数据
     * @param  string $data       待解密数据
     * @param  mixed  $privateKey 私钥数据
     * @return string|false
     */
    public static function privateDecode($data, $privateKey)
    {
        $res = openssl_private_decrypt($data, $response, $privateKey);
        return $res ? $response : false;
    }

    /**
     * 公钥加密数据
     * @param  string $data       待加密数据
     * @param  mixed  $publicKey  公钥数据
     * @return string|false
     */
    public static function publicEncode($data, $publicKey)
    {
        $res = openssl_public_encrypt($data, $response, $publicKey);
        return $res ? $response : false;
    }

    /**
     * 公钥加密数据
     * @param  string $data       待解密数据
     * @param  mixed  $publicKey  公钥数据
     * @return string|false
     */
    public static function publicDecode($data, $publicKey)
    {
        $res = openssl_public_decrypt(self::base58decode($data), $response, $publicKey);
        return $res ? $response : false;
    }
}