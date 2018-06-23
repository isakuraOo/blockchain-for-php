<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Validation;

use Joosie\Blockchain\Providers\Service;
use Joosie\Blockchain\Stores\BlockInterface;

/**
 * 共识机制类
 * 节点之间通过何种约束规范达成最终的共识，将在
 * 实现了 ConsensusInterface 的类中进行处理
 */
class Consensus extends Service implements ConsensusInterface
{
    /**
     * 区块难度系数
     */
    const BLOCK_DIFFICULTY = 5;

    /**
     * 待校验区块实例
     * @var \Joosie\Blockchain\Stores\BlockInterface
     */
    protected $block;

    /**
     * 验证区块哈希是否符合难度要求
     * @param  BlockInterface $block 区块对象
     * @return boolean
     */
    public function validate(BlockInterface $block)
    {
        $this->block = $block;

        $this->beforeValidate();

        $difficulty = $this->getCurrentDifficulty();
        $prefixNum = sprintf('%0' . $difficulty . 'd', '');
        if (substr($block->hash, 2, $difficulty) !== $prefixNum) {
            return false;
        }

        $this->afterValidate();

        return true;
    }

    /**
     * 区块验证操作开始前的处理
     * @return void
     */
    public function beforeValidate()
    {
        # TODO something
    }

    /**
     * 区块验证操作通过后的处理
     * 区块验证没有通过的情况下不会被调用
     * @return void
     */
    public function afterValidate()
    {
        
    }

    /**
     * 获取当前区块难度系数
     * @return interge
     */
    public function getCurrentDifficulty()
    {
        $difficulty = $this->blockchainManager->config['difficulty'];
        return !empty($difficulty) ? $difficulty : self::BLOCK_DIFFICULTY;
    }
}