<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Services\Swoole;

use Joosie\Blockchain\Services\SocketServerAdapter;

/**
* 区块链通信服务 Swoole 实现
*/
class BlockchainSwooleServer extends SocketServerAdapter
{
    private $workNum = null;

    private $reactorNum = null;

    private $maxRequest = null;

    private $maxConnect = null;

    private $backlog = 20;
    
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * 服务启动
     * @param  Object $serv 服务实例
     */
    public function onStart($serv)
    {
        
    }

    /**
     * 连接进入
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onConnect($serv, $fd)
    {
        echo sprintf("Client %s connect!\n", $fd);
    }

    /**
     * 数据接收
     * @param  Object  $serv   服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  String  $data   数据内容
     */
    public function onReceive($serv, $fd, $fromId, $data)
    {
        echo sprintf("Receive data: %s\n", $data);
    }

    /**
     * 连接关闭
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onClose($serv, $fd)
    {
        echo sprintf("Client %s close!\n", $fd);
    }
}