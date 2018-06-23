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
     * @var Joosie\Blockchain\Server\SocketServerInterface
     */
    protected $engine;
    
    /**
     * 获取服务实例
     * @param array  $config                Socket 参数配置
     * @param mixed  $clientEngineClassName 类名，建议使用 XXX\XXX::class 的形式
     * @return \Joosie\Blockchain\Server\SocketServerInterface
     */
    public function getServer(array $config = [], $clientEngineClassName = null)
    {
        if (empty($clientEngineClassName)) {
            $clientEngineClassName = BlockchainSwooleServer::class;
        }

        $this->engine = new $clientEngineClassName($this->blockchainManager);
        return $this->engine;
    }

    /**
     * 发送 UDP 数据包
     * @param  string $message 数据包处理类
     * @return boolean
     */
    public function sendto(string $message)
    {
        return $this->engine->sendto($message);
    }
}