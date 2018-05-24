<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Server;

use Joosie\Blockchain\Server\Swoole\BlockchainSwooleServer;
use Joosie\Blockchain\Providers\Service;

/**
 * Socket 服务端
 */
class SocketServer extends Service
{
    /**
     * Socket 引擎
     * @var null|Joosie\Blockchain\Server\SocketServerInterface
     */
    static protected $engine = null;
    
    /**
     * 获取服务实例
     * @param array  $config                Socket 参数配置
     * @param mixed  $clientEngineClassName 类名，建议使用 XXX\XXX::class 的形式
     * @return \Joosie\Blockchain\Server\SocketServerInterface
     */
    public static function getServer(array $config = [], $clientEngineClassName = null)
    {
        if (empty($clientEngineClassName)) {
            $clientEngineClassName = BlockchainSwooleServer::class;
        }

        self::$engine = new $clientEngineClassName();
        return self::$engine;
    }
}