<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client\Swoole;

use swoole_client;
use Joosie\Blockchain\Client\SocketClientAdapter;
use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Console\Message\MsgHandler;

/**
* 区块链通信服务 Swoole 实现
*/
class BlockchainSwooleClient extends SocketClientAdapter
{
    /**
     * 实例初始化方法
     */
    public function init()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_UDP);
        $this->client->connect($this->ip, $this->port);
    }

    /**
     * 连接进入
     * @param  Object  $client  服务实例
     */
    public function onConnect($client)
    {
        echo sprintf("Server %s connect!", $fd);
    }

    /**
     * TCP 数据接收
     * @param  Object  $client 服务实例
     * @param  String  $data   数据内容
     */
    public function onReceive($client, $data)
    {
        echo sprintf("Receive data: %s", $data);
    }

    /**
     * 连接关闭
     * @param  Object  $client  服务实例
     * @param  Integer $fd      连接标识
     */
    public function onClose($client)
    {
        echo sprintf(" %s close!", $fd);
    }

    /**
     * 服务配置设置
     * @param array $conf 配置数组
     */
    public function set(array $conf)
    {
        if (empty($conf)) {
            throw new BlockchainClientException('Invalid socket server configure!');
        }
        $this->client->set($conf);
        return $this;
    }

    /**
     * 加入组播
     * @return \Joosie\Blockchain\Client\Swoole\BlockchainSwooleServer
     */
    public function joinMulticast()
    {
        $socket = $this->client->getSocket();
        $res = socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, $this->multicastOption);
        if (!$res) {
            throw new BlockchainClientException('Set socket options fail!');
        }
        return $this;
    }

    /**
     * 发送 UDP 数据包
     * @param  string $message 数据包处理类
     * @return boolean
     */
    public function sendto(string $message)
    {
        $data = MsgHandler::encrypt($message);
        return $this->client->sendto($this->multicastOption['group'], $this->port, $data);
    }
}