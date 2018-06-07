<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Providers;

/**
 * 区块链扩展服务提供者基类
 */
class Service
{
    /**
     * 区块链主控类实例
     * @var \Joosie\Blockchain\BlockchainManager
     */
    protected $blockchainManager;

    /**
     * 服务实例
     * @var static
     */
    protected static $instance;
    
    /**
     * 创建一个服务提供者实例
     * @param \Joosie\Blockchain\BlockchainManager $blockchainManager
     */
    public function __construct($blockchainManager)
    {
        $this->blockchainManager = $blockchainManager;
        $this->init();
    }

    /**
     * 获取一个服务实例
     * @param  \Joosie\Blockchain\BlockchainManager $blockchainManager
     * @return static
     */
    public static function getInstance($blockchainManager)
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($blockchainManager);
        }
        return static::$instance;
    }

    /**
     * 实例初始化处理
     * @return void
     */
    protected function init() {}


}