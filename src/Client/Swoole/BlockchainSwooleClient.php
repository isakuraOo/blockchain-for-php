<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client\Swoole;

use Joosie\Blockchain\Client\SocketClientAdapter;
use Joosie\Blockchain\Transaction;

/**
* 区块链通信服务 Swoole 实现
*/
class BlockchainSwooleClient extends SocketClientAdapter
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
    
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            }
        }
        $this->transaction = new Transaction();
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
     * 服务设置
     * @param string $name   服务名
     * @param mixed  $handle 服务处理内容
     */
    public function set(string $name, $handle)
    {
        $this->data[$name] = $handle;
    }

    function __get($name)
    {
        if (!isset($this->$name) || is_null($this->$name)) {
            if (isset($this->data[$name])) {
                return $this->$name = $this->data[$name];
            }
        }
    }
}