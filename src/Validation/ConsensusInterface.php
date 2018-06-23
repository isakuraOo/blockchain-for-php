<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Validation;

use Joosie\Blockchain\Stores\BlockInterface;

interface ConsensusInterface
{
    public function validate(BlockInterface $block);

    public function beforeValidate();

    public function afterValidate();

    public function getCurrentDifficulty();
}