<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainException;
use Joosie\Blockchain\Providers\Service;

/**
 * 区块链服务容器类
 */
class Container
{

    /**
     * 服务定义列表
     * @var array
     */
    protected $definitions = [];

    /**
     * 服务实例列表
     * @var array
     */
    protected $instances = [];

    /**
     * 待实例化服务列表
     * @var array
     */
    protected $aliases = [];

    /**
     * 创建一个容器实例
     * @param \Joosie\Blockchain\BlockchainBase $blockchainManager
     */
    public function __construct($blockchainManager)
    {
        $this->blockchainManager = $blockchainManager;
    }

    /**
     * 服务设置
     * @param String $classAlias 服务名
     * @param Mixed  $definition 服务定义
     */
    public function set(string $classAlias, $definition = null)
    {
        // 如果注册的服务定义是一个实例，需要校验实例是否是 Service 的实现
        // 如果不是则不允许注册
        if (!is_callable($definition) && is_object($definition) && !$definition instanceof Service) {
            throw new BlockchainException(
                sprintf('The service [%s] must be instance of %s in blockchain.', $classAlias, Service::class)
            );
        }

        $this->definitions[$classAlias] = $definition;
        $this->aliases[$classAlias] = true;
        if (isset($this->instances[$classAlias])) {
            unset($this->instances[$classAlias]);
        }
    }

    /**
     * 获取服务实例
     * @param  string $classAlias 服务名
     * @return Object             服务实例
     */
    public function get(string $classAlias)
    {
        // 如果服务未定义，直接抛异常返回
        if (!isset($this->definitions[$classAlias])) {
            throw new BlockchainException(sprintf('Service does not exist of "%s"', $classAlias));
        }
        // 如果已存在实例化的实例，直接返回
        if (isset($this->instances[$classAlias])) {
            return $this->instances[$classAlias];
        }

        // 判断服务声明的类型，根据不同的类型进行不同的实例化处理
        // 目前仅支持 回调函数、字符串、对象 三种声明方式
        $definition = $this->definitions[$classAlias];
        if (is_callable($definition)) {
            $service = call_user_func($definition, $this->blockchainManager);
        } elseif (is_string($definition)) {
            $service = new $definition($this->blockchainManager);
        } elseif (is_object($definition)) {
            $service = $definition;
        } else {
            throw new BlockchainException('Invalid definition for ' . $classAlias);
        }

        // 服务实例检测
        // 只有实现 \Joosie\Blockchain\Providers\Service 的实例才能被允许
        if (!$service instanceof Service) {
            throw new BlockchainException(
                sprintf('The service "%s" must be instance of %s in blockchain.', $classAlias, Service::class)
            );
        }
        $this->instances[$classAlias] = $service;
        unset($this->aliases[$classAlias]);
        return $service;
    }

    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __isset($name)
    {
        return isset($this->definitions[$name]);
    }
}