<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainConfigException;
use ArrayAccess;

/**
 * 配置管理类
 */
class ConfigManager implements ArrayAccess
{
    /**
     * 默认支持的配置
     * @var array
     */
    protected $defaultConf = [];

    /**
     * 扩展配置
     * @var array
     */
    protected $conf = [];

    /**
     * 构造方法
     * @param array $config 外部配置数组
     */
    function __construct(array $config = [])
    {
        $this->defaultConf = require_once(__DIR__ . 'Config/blockchain.php');
        $this->conf = $config;
    }

    public function offsetExists($offset)
    {
        return isset($this->defaultConf[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->defaultConf[$offset])) {
            throw new BlockchainConfigException('Invalid blockchain config!');
        }

        return isset($this->conf[$offset]) ? $this->conf[$offset] : $this->defaultConf[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!isset($this->defaultConf[$offset])) {
            throw new BlockchainConfigException('Does not exist blockchain config!');
        }

        $this->conf[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new BlockchainConfigException('Not allow operate for unset blockchain config!');
    }
}