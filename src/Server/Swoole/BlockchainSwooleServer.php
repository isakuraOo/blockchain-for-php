<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Server\Swoole;

use Joosie\Blockchain\Server\SocketServerAdapter;
use Joosie\Blockchain\Exceptions\BlockchainServerException;
use swoole_server;

/**
* 区块链通信服务 Swoole 实现
*/
class BlockchainSwooleServer extends SocketServerAdapter
{
    protected $workNum = null;

    protected $reactorNum = null;

    protected $maxRequest = null;

    protected $maxConnect = null;

    protected $backlog = 20;

    protected $data = [];

    /**
     * 事务处理实例
     * @var null
     */
    public $transaction = null;
    
    /**
     * 构造方法
     * @param array $config [description]
     */
    public function __construct()
    {
        $this->serv = new swoole_server($this->ip, $this->port, SWOOLE_BASE, SWOOLE_SOCK_UDP);
    }

    /**
     * 服务启动
     * @return Boolean
     */
    public function start()
    {
        return $this->serv->start();
    }

    /**
     * 回调事件注册
     * @param  string $event    事件
     * @param  mixed  $callback 回调处理
     * @return integer
     */
    public function on(string $event, $callback)
    {
        return $this->serv->on($event, $callback);
    }

    /**
     * 服务启动
     * @param  Object $serv 服务实例
     */
    public function onStart($serv)
    {
        echo "Hello blockchain!\n";
    }

    /**
     * 连接进入
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onConnect($serv, $fd)
    {
        echo sprintf("Client %s connect!", $fd);
    }

    /**
     * TCP 数据接收
     * @param  Object  $serv   服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  String  $data   数据内容
     */
    public function onReceive($serv, $fd, $fromId, $data)
    {
        echo sprintf("Receive data: %s", $data);
    }

    public function onPacket($serv, $data, $address)
    {
        // $serv->sendto('233.233.233.233', 9607, "Hello swoole");
        var_dump($address, strlen($data));
        echo sprintf("onPacket content: %s\n", $data);
    }

    /**
     * 连接关闭
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onClose($serv, $fd)
    {
        echo sprintf("Client %s close!", $fd);
    }

    /**
     * 服务配置设置
     * @param array $conf 配置数组
     */
    public function set(array $conf)
    {
        if (empty($conf)) {
            throw new BlockchainServerException('Invalid socket server configure!');
        }
        $this->serv->set($conf);
        return $this;
    }

    /**
     * 加入组播
     * @return \Joosie\Blockchain\Server\Swoole\BlockchainSwooleServer
     */
    public function joinMulticast()
    {
        $socket = $this->serv->getSocket();
        $res = socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, $this->multicastOption);
        if (!$res) {
            throw new BlockchainServerException('Set socket options fail!');
        }
        return $this;
    }
}