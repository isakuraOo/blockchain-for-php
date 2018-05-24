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
     * 创建一个服务提供者实例
     * @param \Joosie\Blockchain\BlockchainManager $blockchainManager
     */
    function __construct($blockchainManager)
    {
        $this->blockchainManager = $blockchainManager;
    }
}