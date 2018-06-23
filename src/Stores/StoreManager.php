<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Providers\Service;
use Joosie\Blockchain\Exceptions\BlockchainServiceException;
use Joosie\Blockchain\Exceptions\BlockchainStoreException;
use Joosie\Blockchain\Stores\Redis\RedisStore;

/**
 * 数据存储管理类
 */
class StoreManager extends Service implements StoreInterface
{
    // 前缀
    const STORE_PREFIX                  = 'joosie_';

    // 区块链完整数据链
    const BLOCK_CHAIN_DETAIL_LIST       = 'blockchain_detail_list';
    // 区块链区块哈希列表（队列）
    const BLOCK_CHAIN_HASH_LIST         = 'blockchain_hash_list';
    // 未使用交易输出
    const UNSPENT_TRANSACTION_OUTPUT    = 'unspent_transaction_output';
    // 已使用交易输出
    const SPENT_TRANSACTION_OUTPUT      = 'spent_transaction_output';
    // 交易记录
    const TRANSACTION_RECORDS           = 'transaction_records';
    // 待确定交易记录
    const NO_CONFIRM_TRANSACTION_RECORDS = 'no_confirm_transaction_records';
    // 地址拥有财产情况
    const WALLET_RESOURCES_INFO         = 'wallet_resources_info';
    // 当前存活节情况
    const ALIVE_WORK_NODE_DETAIL        = 'alive_work_node_detail';
    // 新区块确认数量
    const CONFIRM_NEW_BLOCK_NUM         = 'confirm_new_block_num';
    // 新区块拒绝数量
    const CANCEL_NEW_BLOCK_NUM          = 'cancel_new_block_num';

    /**
     * 存储引擎
     * @var \Joosie\Blockchain\Stores\StoreContractInterface
     */
    protected $engine;

    /**
     * 当前操作指向的数据存储区
     * @var String
     */
    protected $bucket;

    /**
     * 存储服务连接
     * 配置数据将从配置参数服务类中获取当前注入的参数，在配置中必须设置 storeConfig
     * store 的相关内容，否则将抛出一个 BlockchainServiceException 的异常
     * 同时存储服务类中必须声明有 create[storeName]Store 的方法，因为我们将通过
     * 该方法实例化一个存储引擎
     * @return self
     */
    public function connect()
    {
        // 配置参数校验
        $configManager = $this->blockchainManager->config;
        if (!isset($configManager['storeConfig'][$configManager['store']])) {
            throw new BlockchainServiceException(
                sprintf('Not found store configure for the [%s]!', $configManager['store'])
            );
        }

        $config = $configManager['storeConfig'][$configManager['store']];
        $storeMethod = 'create' . ucfirst($configManager['store']) . 'Store';

        // 检查是否存在对应存储服务的创建方法
        if (!method_exists($this, $storeMethod)) {
            throw new BlockchainServiceException(
                sprintf('Not found method [%s] from [%s]', $storeMethod, self::class)
            );
        }

        // 创建对应的存储服务并将其作为存储引擎
        $this->engine = $this->{$storeMethod}($config);
        return $this;
    }

    /**
     * 关闭存储服务
     * @return Boolean
     */
    public function close()
    {
        return $this->engine->close();
    }

    /**
     * 设置当前存储区
     * @param  String $bucket 存储区名称
     * @return self
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * 获取存储区名称
     * 主要作用于添加存储区前缀名，当提供了存储区参数时直接返回接上前缀的名称
     * 如未传递参数则返回当前设置的存储区
     * @param  string $bucket 存储区名称
     * @return string
     */
    public function getBucket($bucket = null)
    {
        if (is_null($bucket)) {
            $bucket = $this->bucket;
        }
        return strpos($bucket, self::STORE_PREFIX) === 0
            ? $bucket : self::STORE_PREFIX . $bucket;
    }

    /**
     * 根据账户获取对应的资产情况
     * @param  String $account 账户地址
     * @return Array
     */
    public function findResourcesInfoByAccount(string $account)
    {
        return $this->engine->from($this->getBucket(self::WALLET_RESOURCES_INFO))
            ->select($account);
    }

    /**
     * 获取最后一个区块的哈希值
     * @return string
     */
    public function getLastBlockHash()
    {
        return $this->engine->from($this->getBucket(self::BLOCK_CHAIN_HASH_LIST))
            ->findLast();
    }

    /**
     * 根据区块哈希值查找区块内容
     * @param  string $hash 区块哈希值
     * @return array
     */
    public function findOneBlockByHash($hash)
    {
        return $this->engine->from($this->getBucket(self::BLOCK_CHAIN_DETAIL_LIST))
            ->select($hash);
    }

    /**
     * 获取区块链长度
     * @return integer
     */
    public function getBlockchainLenght()
    {
        return $this->engine->from($this->getBucket(self::BLOCK_CHAIN_DETAIL_LIST))
            ->count();
    }

    /**
     * 插入一个区块
     * @param  string $hash      区块哈希值
     * @param  string $blockData 区块数据
     * @return boolean
     */
    public function insertOneBlock(string $hash, string $blockData)
    {
        $this->engine->beginTransaction();
        $this->engine->from($this->getBucket(self::BLOCK_CHAIN_DETAIL_LIST))
            ->bind($hash, $blockData)
            ->insert();
        $this->engine->from($this->getBucket(self::BLOCK_CHAIN_HASH_LIST))
            ->bind($hash)
            ->insertToRight();

        $result = $this->engine->execute();
        return $result !== false;
    }

    /**
     * 保存存活节点情况
     * @param  string $nodeName 节点名称标识
     * @param  string $value    节点数据
     * @return boolean
     */
    public function saveAliveNode(string $nodeName, string $value)
    {
        return $this->engine->from($this->getBucket(self::ALIVE_WORK_NODE_DETAIL))
            ->bind($name, $value)
            ->insert();
    }

    /**
     * 获取当前在线节点数量
     * @return integer
     */
    public function getAliveNodeCount()
    {
        return $this->engine->from($this->getBucket(self::ALIVE_WORK_NODE_DETAIL))
            ->count();
    }

    /**
     * 新区块节点确认数量加一
     * @return integer|false
     */
    public function increaseNewBlockConfirm()
    {
        return $this->engine->from($this->getBucket(self::CONFIRM_NEW_BLOCK_NUM))
            ->increase();
    }

    /**
     * 新区块节点拒绝数量加一
     * @return integer|false
     */
    public function increaseNewBlockCancel()
    {
        return $this->engine->from($this->getBucket(self::CANCEL_NEW_BLOCK_NUM))
            ->increase();
    }

    /**
     * 获取当前新区块确认的节点数量
     * @return integer
     */
    public function getCurrentConfirmNumForNewBlock()
    {
        return $this->engine->from($this->getBucket(self::CONFIRM_NEW_BLOCK_NUM))
            ->get();
    }

    /**
     * 校验新区块节点确认情况，是否可以进行区块插入的操作
     * 当前采取共识策略：只有当超过 50% 的节点确认区块合
     * 法后才算是达成共识
     * @return boolean
     */
    public function checkNewBlockConfirm()
    {
        $aliveNodeCount = $this->getAliveNodeCount();
        $confirmNum = $this->getCurrentConfirmNumForNewBlock();
        return ($confirmNum / $aliveNodeCount) > 0.5;
    }

    /**
     * 获取待确认的交易数据
     * @return array
     */
    public function getNoConfirmTransactions()
    {
        return $this->engine->from($this->getBucket(self::NO_CONFIRM_TRANSACTION_RECORDS))
            ->getAllList();
    }

    /**
     * 确认交易记录
     * 新区块生成通过验证后需要将区块中成功存储的交易改为已确认交易
     * @param  array  $transactions 需要确认的订单列表数组
     * @return void
     */
    public function confirmTransactions(array $transactions)
    {
        $transactionCount = count($transactions);
        // 从待确认队列移除对应数量的交易
        $this->engine->from(
            $this->getBucket(self::NO_CONFIRM_TRANSACTION_RECORDS)
        )->listPush($transactionCount);

        // 交易数据插入已完成的队列
        $store = $this->engine->from($this->getBucket(self::TRANSACTION_RECORDS));
        foreach ($transactions as $transaction) {
            $store->bind($transaction)->insertToRight();
        }
    }

    /**
     * 创建一个 Redis 存储实例
     * @param  array  $config 配置参数
     * @return \Joosie\Blockchain\Stores\Redis\RedisStore
     */
    protected function createRedisStore(array $config)
    {
        return new RedisStore($config);
    }
}