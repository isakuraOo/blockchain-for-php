<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Traits;

use Joosie\Blockchain\BlockchainBase;

trait BlockchainManagerTrait
{
    public $blockchainManager = null;

    public function setBlockchainManager(BlockchainBase $manager)
    {
        $this->blockchainManager = $manager;
    }
}