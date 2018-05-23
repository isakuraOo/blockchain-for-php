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
     * Socket 服务客户端
     * @var Joosie\Blockchain\Client\SocketClient
     */
    protected $sockClient = null;

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
     * 服务容器初始化
     */
    protected function initContainer()
    {
        $this->container = new Container();
        if (isset($this->config['services'])) {
            foreach ($this->config['services'] as $serviceName => $serviceDefinition) {
                $this->container->set($serviceName, $serviceDefinition);
            }
        }
    }
}