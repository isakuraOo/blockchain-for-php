<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Message;

/**
 * 消息加密类
 */
class MsgEncrypt
{
    /**
     * 数据加密
     * @param  string $data       明文数据
     * @param  string $privateKey 密钥
     * @param  string $iv         向量
     * @return string             密文数据
     */
    public static function encrypt(string $data, string $privateKey, string $iv)
    {
        return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $privateKey, OPENSSL_RAW_DATA, base64_decode($iv)));
    }
}