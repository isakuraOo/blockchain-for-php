<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client;

use Joosie\Blockchain\Providers\Service;

/**
* 
*/
class SocketClientAdapter extends Service implements SocketClientInterface
{
    /**
     * 客户端服务实例
     * @var null\SwooleClient
     */
    protected $client = null;

    /**
     * 组播配置
     * @var array
     */
    protected $multicastOption = ['group' => '233.233.233.233', 'interface' => 'en0'];

    /**
     * 监听地址
     * @var string
     */
    protected $ip = '0.0.0.0';

    /**
     * 监听端口
     * @var integer
     */
    protected $port = 9608;

    /**
     * 连接进入
     * @param  Object  $client 服务实例
     */
    public function onConnect($client) {}

    /**
     * TPC 数据接收
     * @param  Object  $client 服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  string  $data   数据内容
     */
    public function onReceive($client, $data) {}

    /**
     * 连接关闭
     * @param  Object  $client  服务实例
     * @param  Integer $fd      连接标识
     */
    public function onClose($client) {}

    /**
     * 配置设置
     * @param Array $conf 配置数组
     */
    public function set(array $conf) {}

    /**
     * 加入组播
     */
    public function joinMulticast() {}
}