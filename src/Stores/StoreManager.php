<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Providers\Service;
use Joosie\Blockchain\Exceptions\BlockchainServerException;
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
    // 地址拥有财产情况
    const WALLET_RESOURCES_INFO         = 'wallet_resources_info';

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
     * store 的相关内容，否则将抛出一个 BlockchainServerException 的异常
     * 同时存储服务类中必须声明有 create[storeName]Store 的方法，因为我们将通过
     * 该方法实例化一个存储引擎
     * @return self
     */
    public function connect()
    {
        // 配置参数校验
        $configManager = $this->blockchainManager->config;
        if (!isset($configManager['storeConfig'][$configManager['store']])) {
            throw new BlockchainServerException(
                sprintf('Not found store configure for the [%s]!', $configManager['store'])
            );
        }

        $config = $configManager['storeConfig'][$configManager['store']];
        $storeMethod = 'create' . ucfirst($configManager['store']) . 'Store';

        // 检查是否存在对应存储服务的创建方法
        if (!method_exists($this, $storeMethod)) {
            throw new BlockchainServerException(
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
     * 创建一个 Redis 存储实例
     * @param  array  $config 配置参数
     * @return \Joosie\Blockchain\Stores\Redis\RedisStore
     */
    protected function createRedisStore(array $config)
    {
        return new RedisStore($config);
    }
}