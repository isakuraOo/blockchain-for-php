<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Providers\Service;

/**
* 区块数据类
*/
class Block extends Service implements BlockInterface
{
    /**
     * 区块所属账户
     * @var string
     */
    protected $belongtoAccount;
    
    /************************************************
     * 
     * 区块头数据
     *
     */
    
    /**
     * 区块编号
     * @var integer
     */
    protected $blockNumber;

    /**
     * 随机数
     * @var string
     */
    protected $nonce;

    /**
     * 难度系数
     * @var integer
     */
    protected $difficulty;

    /**
     * 版本号
     * @var string
     */
    protected $version;

    /**
     * 区块生成的时间戳（秒）
     * @var integer
     */
    protected $timestamp;

    /**
     * 上一个区块的 Hash 值
     * @var string
     */
    protected $prevHash;

    /**
     * 当前区块的数据摘要 Hash 值
     * @var string
     */
    protected $hash;

    /**
     * 当前区块中，区块体账单数据的摘要值
     * 通过 Merkle Tree 算法取 Root Node 的值作为数据体的摘要值
     * @var string
     */
    protected $dataHash;


    /************************************************
     * 
     * 区块体数据
     *
     */

    /**
     * 账单数据
     * 数据默认使用 Json 格式存储
     * @var array
     */
    protected $transactionData = [];

    /**
     * 账单数据生成的 Merkle Tree 数据数组
     * 每个树节点由三个属性构成 value 哈希值 leftChildNode 左子节点
     * rightChildNode 右子节点
     * @var array
     */
    protected $merkleTreeData = [];

    /**
     * 区块头属性列表
     * @var array
     */
    static protected $headerKeys = [
        'blockNumber',
        'nonce',
        'difficulty',
        'version',
        'timestamp',
        'prevHash',
        'hash',
        'dataHash'
    ];

    /**
     * 区块体属性列表
     * @var array
     */
    static protected $bodyKeys = [
        'transactionData',
        'merkleTreeData'
    ];

    /**
     * 根据区块数据数组创建一个区块实例
     * @param  array  $data 区块数据数组
     * [
     *     'header' => [...static::$headerKeys],
     *     'body'   => [...static::$bodyKeys],
     *     'belongtoAccount' => ''
     * ]
     * @return static
     */
    public function create(array $data)
    {
        $attributes = [];
        // 检查区块所有者
        if (!isset($data['belongtoAccount'])) {
            throw new BlockchainBlockException('Invalid block owner');
        }

        // 检查区块头数据
        foreach (static::$headerKeys as $field) {
            if (!isset($data['header'][$field])) {
                throw new BlockchainBlockException(
                    'Required attributes [%s] from block header', $field
                );
            }

            $attributes[$field] = $data['header'][$field];
        }

        // 检查区块体数据
        foreach (static::bodyKeys as $field) {
            if (!isset($data['body'][$field])) {
                throw new BlockchainBlockException(
                    'Required attributes [%s] from block body', $field
                );
            }

            $attributes[$field] = $data['body'][$field];
        }

        // 根据区块数据创建一个区块实例
        $instance = new static;
        foreach ($attributes as $name => $value) {
            $instance->{$name} = $value;
        }
        $instance->belongtoAccount = $data['belongtoAccount'];

        return $instance;
    }

    /**
     * 获取最后一个区块
     * @return \Joosie\Blockchain\Storage\Block
     */
    public function getLastBlock()
    {
        $lastBlockHash = $this->blockchainManager->store->getLastBlockHash();
        return $this->blockchainManager->store->findOneBlockByHash($lastBlockHash);
    }

    /**
     * 准备一个新区块实例
     * @return \Joosie\Blockchain\Stores\NewBlock
     */
    public function readyNewBlock()
    {
        $prevHash = $this->blockchainManager->store->getLastBlockHash();
        $newBlock = new NewBlock($this->blockchainManager);
        return $newBlock->setPrevHash($prevHash === false ? '' : $prevHash)
            ->setDifficulty(
                $this->blockchainManager->consensus->getCurrentDifficulty()
            );
    }

    /**
     * 获取区块头部数据
     * @return array
     */
    public function getHeader()
    {
        $header = [];
        foreach (static::$headerKeys as $headerKey) {
            $header[$headerKey] = $this->{$headerKey};
        }
        ksort($header);
        return $header;
    }

    /**
     * 获取区块体数据
     * @return array
     */
    public function getBody()
    {
        $body = [];
        foreach (static::$bodyKeys as $bodyKey) {
            $body[$bodyKey] = $this->{$bodyKey};
        }
        ksort($body);
        return $body;
    }

    /**
     * 获取区块完整数据
     * [
     *     'header' => [区块头数据],
     *     'body'   => [区块体数据],
     *     'belongtoAccount' => '区块所有者'
     * ]
     * @return array
     */
    public function getBlockData()
    {
        return [
            'header'    => $this->getHeader(),
            'body'      => $this->getBody(),
            'belongtoAccount' => $this->belongtoAccount,
        ];
    }

    /**
     * 生成随机字符串
     * @param  integer $len 长度
     * @return string
     */
    protected function generateNonce($len = 64)
    {
        $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= $char[rand(0, strlen($char) - 1)];
        }
        return $result;
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}