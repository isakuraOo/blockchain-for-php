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
class BlockchainManager extends BlockchainBase
{
    /**
     * 当前区块
     * @var Joosie\Blockchain\Storage\Block
     */
    protected $nowBlock = null;

    /**
     * 构造
     * @param ConfigManager|null $config 配置类
     */
    public function __construct(ConfigManager $config = null)
    {
        parent::__construct($config);
        static::setInstance($this);
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

    /**
     * 获取服务容器
     * @return Joosie\Blockchain\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * 魔术方法
     * @param  String $name 属性名
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } elseif (isset($this->container->$name)) {
            return $this->container->$name;
        } else {
            throw new BlockchainException("Invalid property of {$name}");
        }
    }
}