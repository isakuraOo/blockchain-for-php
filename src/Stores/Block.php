<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Providers\Service;

/**
* 区块数据类
*/
class Block extends Service
{
    
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
     * @var string
     */
    protected $transactionData;

    /**
     * 账单数据生成的 Merkle Tree
     * Merkle Tree 默认使用 Json 格式存储
     * @var string
     */
    protected $merkleTreeData;

    /**
     * 区块头属性列表
     * @var Array
     */
    protected $headerKeys = [
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
     * 获取区块体交易数据
     * @return Array
     */
    public function getTransactionData()
    {
        static $data = null;
        if (is_null($data)) {
            $data = json_decode($this->transactionData, true) ?: [];
        }
        return $data;
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
        return NewBlock::getInstance($this->blockchainManager)
            ->setPrevHash($prevHash === false ? '' : $prevHash)
            ->setDifficulty(NewBlock::DEFAULT_DIFFICULTY);
    }

    /**
     * 获取区块头部数据
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->headerKeys as $header) {
            $headers[$header] = $this->{$header};
        }
        ksort($headers);
        return $headers;
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