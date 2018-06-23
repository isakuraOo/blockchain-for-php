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
    const SIGN = '057cce7cf0d57da816993107dea3f81d';

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
     * 数据加密
     * @return string 密文数据
     */
    public static function encrypt($message, $type = MsgType::TYPE_COMMON)
    {
        $message = Data::base58encode($message);
        $data = sprintf('%s%s%s', self::SIGN, $type, $message);
        return Data::encrypt($data);
    }

    /**
     * 数据解密
     * @return string|false 明文数据
     */
    public static function decrypt($data)
    {
        $data = Data::decrypt($data);
        if (substr($data, 0, 32) != self::SIGN) {
            return false;
        }

        return [
            'type'  => substr($data, strlen(self::SIGN), 4),
            'data'  => Data::base58decode(substr($data, strlen(self::SIGN) + 4))
        ];
    }
}