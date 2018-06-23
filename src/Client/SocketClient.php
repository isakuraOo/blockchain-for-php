<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client;

use Joosie\Blockchain\Client\Swoole\BlockchainSwooleClient;
use Joosie\Blockchain\Providers\Service;

/**
 * Socket 客户端
 */
class SocketClient extends Service
{
    /**
     * Socket 客户端引擎实例
     * @var Joosie\Blockchain\Client\SocketClientInterface
     */
    protected $engine;
    
    /**
     * 构造方法
     * @param array  $config                Socket 参数配置
     * @param mixed  $clientEngineClassName 类名，建议使用 XXX\XXX::class 的形式
     * @return \Joosie\Blockchain\Client\SocketClientInterface
     */
    public function getClient(array $config = [], $clientEngineClassName = null)
    {
        if (empty($clientEngineClassName)) {
            $clientEngineClassName = BlockchainSwooleClient::class;
        }
        
        $this->engine = new $clientEngineClassName($this->blockchainManager);
        return $this->engine;
    }
}