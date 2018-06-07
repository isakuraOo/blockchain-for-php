<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores\Redis;

use Joosie\Blockchain\Stores\StoreContractInterface;
use Joosie\Blockchain\Exceptions\BlockchainStoreException;
use Redis;

/**
 * Redis 存储服务
 */
class RedisStore implements StoreContractInterface
{
    // 默认连接地址
    const DEFAULT_HOST = '127.0.0.1';
    // 默认连接端口
    const DEFAULT_PORT = '6379';

    /**
     * Redis 实例
     * @var \Redis
     */
    protected $redis;

    /**
     * 当前操作 Key
     * @var string
     */
    protected $bucket;

    /**
     * 查询条件
     * @var mixed
     */
    protected $condition;
    
    /**
     * 创建一个 Redis 存储实例
     * @param array $config Redis 配置参数
     * [
     *     'host' => 连接地址,
     *     'port' => 连接端口,
     *     'auth' => 连接密码
     * ]
     */
    public function __construct(array $config)
    {
        $this->redis = new Redis();
        $this->redis->connect(
            $config['host'] ?: self::DEFAULT_HOST,
            $config['port'] ?: self::DEFAULT_PORT
        );

        if (isset($config['auth']) && !empty($config['auth'])) {
            $this->redis->auth($config['auth']);
        }
    }

    /**
     * 设置查询 key
     * @param  string $bucket 操作 Key 名
     * @return self
     */
    public function from(string $bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * 设置查询字段
     * @param  array|string $name 查询字段内容
     * @return self
     */
    public function select($name)
    {
        if (is_string($name)) {
            return $this->redis->hget($this->bucket, $name);
        } elseif (is_array($name)) {
            return $this->redis->hmget($this->bucket, $name);
        } else {
            throw new BlockchainStoreException(sprintf('Invalid field type: [%s]', $name));
        }
    }

    public function insert()
    {
        # TODO 插入数据
    }

    public function delete()
    {
        # TODO 删除数据
    }

    public function count()
    {
        return $this->redis->hlen($this->bucket);
    }

    public function get()
    {
        return $this->redis->hgetall($this->bucket);
    }

    /**
     * 关闭 Redis 服务连接
     * @return void
     */
    public function close()
    {
        return $this->redis->close();
    }

    /**
     * 查询最后一条数据（仅限队列）
     * @return string
     */
    public function findLast()
    {
        $len = $this->redis->llen($this->bucket);
        $result = $this->redis->lrange($this->bucket, $len - 1, -1);
        return !empty($result) ? $reuslt[0] : false;
    }
}