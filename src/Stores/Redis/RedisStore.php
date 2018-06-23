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
     * 字段名
     * @var string
     */
    protected $field;

    /**
     * 待处理数据
     * @var mixed
     */
    protected $data;

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

    /**
     * 数据绑定
     * @param  mixed  $field 数据字段|数据内容
     * @param  mixed  $data  数据内容，不提供该参数的时候使用 $field 作为数据内容
     * @return self
     */
    public function bind($field, $data = null)
    {
        if (is_null($data)) {
            $this->data = $field;
        } else {
            $this->field = $field;
            $this->data = $data;
        }
        return $this;
    }

    /**
     * 插入一条数据
     * @return integer 0|1
     */
    public function insert()
    {
        return $this->redis->hset($this->bucket, $this->field, $this->data);
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
        return $this->redis->get($this->bucket);
    }

    /**
     * 获取队列所有数据
     * @return array
     */
    public function getAllList()
    {
        return $this->redis->lrange($this->bucket, 0, -1);
    }

    public function getAll()
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
     * 开启事务
     * @return self
     */
    public function beginTransaction()
    {
        $this->redis->multi(Redis::MULTI);
        return $this;
    }

    /**
     * 事务提交
     * @return array|false
     */
    public function execute()
    {
        return $this->redis->exec();
    }

    /**
     * 查询最后一条数据（仅限队列）
     * @return string
     */
    public function findLast()
    {
        $len = $this->redis->llen($this->bucket);
        $result = $this->redis->lrange($this->bucket, $len - 1, -1);
        return !empty($result) ? $result[0] : false;
    }

    /**
     * 向队列头部插入数据
     * @return boolean
     */
    public function insertToLeft()
    {
        return !!$this->redis->lpush($this->bucket, $this->data);
    }

    /**
     * 向队列尾部插入数据
     * @return boolean
     */
    public function insertToRight()
    {
        return !!$this->redis->rpush($this->bucket, $this->data);
    }

    /**
     * 队列修剪，将队列前 $count 个元素移除
     * @param  integer $count 需要移除的数量
     * @return boolean
     */
    public function listPush($count = 1)
    {
        return $this->redis->ltrim($this->bucket, $count, -1);
    }

    /**
     * 递增
     * @return integer|false
     */
    public function increase()
    {
        return $this->redis->incr($this->bucket);
    }

    /**
     * 递减
     * @return integer|false
     */
    public function decrease()
    {
        return $this->redis->decr($this->bucket);
    }
}