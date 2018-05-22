<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Storage\Block;
use Joosie\Blockchain\Client\SocketClient;
use Joosie\Blockchain\Exceptions\BlockchainException;

/**
* 区块链主类
*/
class Blockchain
{
    /**
     * 当前区块
     * @var Joosie\Blockchain\Storage\Block
     */
    protected $nowBlock = null;

    /**
     * Socket 服务客户端
     * @var Joosie\Blockchain\Client\SocketClient
     */
    protected $sockClient = null;

    /**
     * 配置参数实例
     * @var \Joosie\Blockchain\Config
     */
    protected $config = null;
    
    /**
     * 构造方法
     * @param array $config 配置参数
     */
    function __construct(array $config = [])
    {
        $this->config = new Config($config);
        $this->sockClient = new SocketClient();
    }

    /**
     * 向当前区块追加一条数据
     * @param  string $data 数据记录
     * @return boolean
     */
    public function pushToNowBlock(string $data)
    {
        # code...
    }

    /**
     * 向当前区块批量追加数据
     * @param  array  $dataArr 数据数组
     * @return boolean
     */
    public function batchPushToNowBlock(array $dataArr)
    {
        # code...
    }

    /**
     * 获取当前区块
     * @return Joosie\Blockchain\Storage\Block 当前区块
     */
    public function getNowBlock()
    {
        # code...
    }

    /**
     * 根据条件查找一个区块
     * @param  mixed $condition 查询条件
     * @return Joosie\Blockchain\Storage\Block 符合条件的区块
     */
    public function findOneBlock($condition)
    {
        # code...
    }

    /**
     * 根据条件查询区块
     * @param  mixed $condition 查询条件
     * 当 $condition 为空时默认查询所有区块
     * @return array
     */
    public function findBlocks($condition = null)
    {
        # code...
    }
}