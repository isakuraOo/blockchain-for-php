<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Server;

use Joosie\Blockchain\Providers\Service;

/**
* 
*/
class SocketServerAdapter extends Service implements SocketServerInterface
{
    /**
     * 服务实例
     * @var null
     */
    protected $serv = null;

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
     * 服务启动
     * @param  Object $serv 服务实例
     */
    public function onStart($serv) {}

    /**
     * 连接进入
     * @param  Object  $serv 服务实例
     * @param  integer $fd   连接标识
     */
    public function onConnect($serv, $fd) {}

    /**
     * TPC 数据接收
     * @param  Object  $serv   服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  string  $data   数据内容
     */
    public function onReceive($serv, $fd, $fromId, $data) {}

    /**
     * UDP 数据接收
     * @param  Object $serv    服务实例
     * @param  String $data    数据内容
     * @param  Integer $address 数据来源
     */
    public function onPacket($serv, $data, $address) {}

    /**
     * 连接关闭
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onClose($serv, $fd) {}

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