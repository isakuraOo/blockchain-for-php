<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client;

use Joosie\Blockchain\Client\Swoole\BlockchainSwooleClient;

/**
 * Socket 客户端
 */
class SocketClient
{
    /**
     * Socket 客户端引擎实例
     * @var null|Joosie\Blockchain\Client\SocketClientInterface
     */
    static protected $engine = null;
    
    /**
     * 构造方法
     * @param array  $config                Socket 参数配置
     * @param mixed  $clientEngineClassName 类名，建议使用 XXX\XXX::class 的形式
     * @return \Joosie\Blockchain\Client\SocketClientInterface
     */
    public static function getClient(array $config = [], $clientEngineClassName = null)
    {
        if (empty($clientEngineClassName)) {
            $clientEngineClassName = BlockchainSwooleClient::class;
        }
        
        self::$engine = new $clientEngineClassName();
        return self::$engine;
    }
}