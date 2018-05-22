<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Client;

/**
* 
*/
interface SocketClientInterface
{

    /**
     * 连接进入
     * @param  Object  $client  服务实例
     * @param  integer $fd      连接标识
     */
    public function onConnect($client);

    /**
     * TCP 数据接收
     * @param  Object  $client 服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  string  $data   数据内容
     */
    public function onReceive($client, $data);

    /**
     * 连接关闭
     * @param  Object  $client  服务实例
     * @param  Integer $fd      连接标识
     */
    public function onClose($client);

    /**
     * 配置设置
     * @param Array $conf 配置数组
     */
    public function set(array $conf);

    /**
     * 加入组播
     */
    public function joinMulticast();
}