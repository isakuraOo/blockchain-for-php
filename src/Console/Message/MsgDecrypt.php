<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Message;

/**
 * 消息解密类
 */
class MsgDecrypt
{
    /**
     * 消息解密
     * @param  string $data       密文数据
     * @param  string $privateKey 密钥
     * @param  string $iv         向量
     * @return string             明文数据
     */
    public static function decrypt(string $data, string $privateKey, string $iv)
    {
        return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $privateKey, OPENSSL_RAW_DATA, base64_decode($iv));
    }
}