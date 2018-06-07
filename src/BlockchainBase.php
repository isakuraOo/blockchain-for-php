<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

/**
 * 区块链服务基类
 */
class BlockchainBase
{
    /**
     * 实例
     * @var static
     */
    protected static $instance;

    /**
     * 配置参数实例
     * @var \Joosie\Blockchain\Config
     */
    protected $config = null;

    /**
     * 服务容器
     * @var \Joosie\Blockchain\Container
     */
    protected $container = null;
    
    /**
     * 构造方法
     * @param ConfigManager|null $config 配置类
     */
    public function __construct(ConfigManager $config = null)
    {
        if (is_null($config)) {
            $config = new ConfigManager();
        }
        
        $this->config = $config;
        $this->initContainer();
    }

    /**
     * 设置实例
     * @param BlockchainBase $instance
     */
    public static function setInstance(BlockchainBase $instance)
    {
        return static::$instance = $instance;
    }

    /**
     * 获取实例
     * @return static
     */
    public static function getInstance(ConfigManager $config = null)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($config);
        }
        return static::$instance;
    }

    /**
     * 服务容器初始化
     * 在此步骤，服务容器会将配置中 services 内的所有服务注入到容器
     */
    protected function initContainer()
    {
        $this->container = new Container($this);
        if (isset($this->config['services'])) {
            foreach ($this->config['services'] as $serviceName => $serviceDefinition) {
                $this->container->set($serviceName, $serviceDefinition);
            }
        }
    }
}