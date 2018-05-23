<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainException;

/**
 * 区块链服务容器类
 */
class Container
{
    /**
     * 存放服务类的定义
     * @var array
     */
    protected $data = [];

    /**
     * 服务设置
     * @param String $classAlias 服务名
     * @param Mixed  $definition 服务定义
     */
    public function set(string $classAlias, $definition = null)
    {
        $this->data[$classAlias] = $definition;
    }

    /**
     * 获取服务实例
     * @param  string $classAlias 服务名
     * @return Object             服务实例
     */
    public function get(string $classAlias)
    {
        if (!isset($this->data[$classAlias])) {
            return new $classAlias;
        }

        $definition = $this->data[$classAlias];
        if (is_callable($definition, true)) {
            return call_user_func($definition);
        } elseif (is_string($definition)) {
            return new $definition;
        } elseif (is_object($definition)) {
            return $definition;
        } else {
            throw new BlockchainException('Invalid definition for ' . $classAlias);
        }
    }

    /**
     * 魔术方法
     * 兼容直接通过类属性的形式获取服务实例
     * @see    $this->get()
     * @param  String $name 服务名
     * @return Object       服务实例
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}