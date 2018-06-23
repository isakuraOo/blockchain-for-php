<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Server\Swoole;

use Joosie\Blockchain\Server\SocketServerAdapter;
use Joosie\Blockchain\Exceptions\BlockchainServerException;
use Joosie\Blockchain\Exceptions\BlockchainBlockException;
use Joosie\Blockchain\Console\Message\MsgHandler;
use Joosie\Blockchain\Console\Message\MsgType;
use Joosie\Blockchain\Event\EventType;
use Joosie\Blockchain\Stores\Block;
use Joosie\Blockchain\Helper\Log;
use swoole_server;

/**
* 区块链通信服务 Swoole 实现
*/
class BlockchainSwooleServer extends SocketServerAdapter
{
    /**
     * 数据包内容
     * @var string
     */
    protected $data;

    /**
     * 实例初始化方法
     */
    public function init()
    {
        $this->serv = new swoole_server(
            $this->ip, $this->port, SWOOLE_BASE, SWOOLE_SOCK_UDP
        );
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
        Log::t('Successfully start!');
        Log::t('Wanna to sync blockchain data, but...');
        Log::t('Start work for new block creating...');
        $this->blockchainManager->block->readyNewBlock()->startWork();
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

    /**
     * UDP 数据接收回调事件
     * @param  Object $serv    服务实例
     * @param  String $data    数据内容
     * @param  Array  $address 数据来源地址信息数据
     */
    public function onPacket($serv, $data, $address)
    {
        $this->handlePacketData($serv, $data, $address);
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

    /**
     * 发送 UDP 数据包
     * @param  string $message 数据包处理类
     * @return boolean
     */
    public function sendto(string $message)
    {
        $data = MsgHandler::encrypt($message);
        Log::t(sprintf("Broadcast data: %s", $data), Log::LOG_TYPE_SUCCESS);
        return $this->serv->sendto($this->multicastOption['group'], $this->port, $data);
    }

    /**
     * 处理接收的 UDP 数据包
     * @param  Object $serv         服务实例
     * @param  String $packetData   数据内容
     * @param  Array  $address      数据来源地址信息数据
     * @return void
     */
    protected function handlePacketData($serv, $packetData, $address)
    {
        $tmpRes = MsgHandler::decrypt($packetData);
        if (!$tmpRes) {
            return false;
        }

        $this->data = $tmpRes['data'];
        switch ($tmpRes['type']) {
            // 普通消息处理
            case MsgType::TYPE_COMMON:
                echo sprintf("%s\n", $this->data);
                break;
            // 新节点接入事件
            case MsgType::TYPE_NEW_NODE_JOIN:
                $this->handleNewNodeJoin($packetData, $address);
                break;
            // 新区块创建成功事件
            case MsgType::TYPE_NEW_BLOCK_CREATE:
                // 触发其它节点新区块创建成功事件，用于停止当前新区块计算工作，进入验证
                $this->blockchainManager->event->trigger(
                    EventType::EVENT_OTHER_NODE_CREATE_BLOCK_SUCC
                );
                // 校验处理新区块数据
                $this->handleNewBlockFromOtherNode();
                // 继续开始工作下一个新区块计算
                $this->blockchainManager->block->readyNewBlock()->startWork();
                break;
            default:
                break;
        }
    }

    /**
     * 处理其他节点生成的新区块
     * 如果区块校验结果是合法的，将通知其他节点自己已经校验通过可以准备上链
     * 同时开始监控当前已校验完成的节点数量是否过半，只有过半才能正式执行上链操作
     * @return void
     */
    protected function handleNewBlockFromOtherNode()
    {
        $data = json_decode($this->data, true);
        $blockHash = $data['block']['header']['hash'];
        $publicKey = Data::base58decode($data['publicKey']);
        $decryptData = Data::publicDecrypt($data['sign'], $publicKey);

        // 校验新区块签名
        if (
            $blockHash !== $decryptData['block']['header']['hash']
            || $data['block']['belongtoAccount'] !== $decryptData['block']['belongtoAccount']
        ) {
            return $this->blockchainManager->event->trigger(
                EventType::EVENT_OTHER_NODE_CRAETE_BLOCK_SIGN_FAIL
            );
        }

        // 校验新区块哈希
        $block = Block::create($data['block']);
        if (!$this->blockchainManager->consensus->validate($block)) {
            return $this->blockchainManager->event->trigger(
                EventType::EVENT_OTHER_NODE_CREATE_BLOCK_HASH_FAIL
            );
        }

        // 区块数据插入
        if (
            !$this->blockchainManager->store->insertOneBlock(
                $block->hash, json_encode($block->getBlockData())
            )
        ) {
            throw new BlockchainBlockException(
                sprintf('Insert block data is fail! Block hash: [%s]', $block->hash)
            );
        }
    }

    /**
     * 新节点接入链网的处理
     * @param  String $data    数据内容
     * @param  Array  $address 数据来源地址信息数据
     * @return void
     */
    protected function handleNewNodeJoin($data, $address)
    {
        $name = Data::base58encode(
            sprintf('%s:%s', $address['address'], $address['port'])
        );
        $value = ['lastAliveTimeAt'   => time()];
        $this->blockchainManager->store->saveAliveNode($name, $value);
    }
}