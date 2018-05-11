<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client;

/**
* 
*/
class SocketClientAdapter implements SocketClientInterface
{
    /**
     * 服务实例
     * @var null
     */
    protected $client = null;

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
}