<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Storage\Block;
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
    
    function __construct(array $config = [])
    {
        # code...
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
}