<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Message;

/**
 * 消息处理类
 */
class MsgHandler
{
    /**
     * 数据加解密私钥
     * @var string
     */
    private $privateKey = 'ycJA1E5zdL93jYsSGXH04wdGNZ7eHoCr';

    /**
     * 数据加解密向量
     * @var string
     */
    private $iv = '7QYgofxP/f85a9bbjus1ZQ==';

    /**
     * 密文消息数据
     * @var string
     */
    protected $encryptData = '';

    /**
     * 明文消息数据
     * @var string
     */
    protected $decryptData = '';

    /**
     * 构造函数
     * @param string $privateKey 数据加解密密钥|明文数据
     * @param string $iv         数据加解密向量
     */
    public function __construct(string $privateKey = null, string $iv = null)
    {
        if (!is_null($privateKey) && is_null($iv)) {
            $this->decryptData = $privateKey;
        }
        if (!is_null($privateKey) && !is_null($iv)) {
            $this->privateKey = $privateKey;
            $this->iv = $iv;
        }
    }

    /**
     * 数据加密
     * @return string 密文数据
     */
    public function encrypt()
    {
        return MsgEncrypt::encrypt($this->decryptData, $this->privateKey, $this->iv);
    }

    /**
     * 数据解密
     * @return string 明文数据
     */
    public function decrypt()
    {
        return MsgDecrypt::decrypt($this->encryptData, $this->privateKey, $this->iv);
    }

    /**
     * 设置密文消息数据
     * @param string $encryptData 密文数据
     * @return Object \Joosie\Blockchain\Console\Message\MsgHandler
     */
    public function setEncryptData($encryptData)
    {
        $this->encryptData = $encryptData;
        return $this;
    }

    /**
     * 设置明文消息数据
     * @param string $decryptData 明文数据
     * @return Object \Joosie\Blockchain\Console\Message\MsgHandler
     */
    public function setDecryptData($decryptData)
    {
        $this->decryptData = $decryptData;
        return $this;
    }
}