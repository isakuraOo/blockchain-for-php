<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Message;

use Joosie\Blockchain\Helper\Data;

/**
 * 消息处理类
 */
class MsgHandler
{

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
     * 消息类型
     * @see \Joosie\Blockchain\Console\Message\MsgType
     * @var null|Integer
     */
    protected $msgType = null;

    /**
     * 构造函数
     * @param String  $msgData  数据内容(目前仅支持接收数据明文)
     * @param Integer $msgType  数据类型
     * @see \Joosie\Blockchain\Console\Message\MsgType
     */
    public function __construct(string $msgData = null, string $msgType = MsgType::TYPE_COMMON)
    {
        $this->decryptData = $privateKey;
        $this->msgType = $msgType;
    }

    /**
     * 数据加密
     * @return string 密文数据
     */
    public function encrypt()
    {
        return Data::encrypt($this->decryptData);
    }

    /**
     * 数据解密
     * @return string 明文数据
     */
    public function decrypt()
    {
        return Data::decrypt($this->encryptData);
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