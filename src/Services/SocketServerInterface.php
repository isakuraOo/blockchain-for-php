<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Services;

/**
* 
*/
interface SocketServerInterface
{
    /**
     * 服务启动
     * @param  Object $serv 服务实例
     */
    public function onStart($serv);

    /**
     * 连接进入
     * @param  Object  $serv 服务实例
     * @param  integer $fd   连接标识
     */
    public function onConnect($serv, $fd);

    /**
     * 数据接收
     * @param  Object  $serv   服务实例
     * @param  Integer $fd     连接标识
     * @param  Integer $fromId 来源标识
     * @param  string  $data   数据内容
     */
    public function onReceive($serv, $fd, $fromId, $data);

    /**
     * 连接关闭
     * @param  Object  $serv 服务实例
     * @param  Integer $fd   连接标识
     */
    public function onClose($serv, $fd);
}