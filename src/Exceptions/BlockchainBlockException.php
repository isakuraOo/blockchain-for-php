<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Exceptions;

/**
 * 区块链区块异常处理类
 */
class BlockchainBlockException extends BlockchainException
{
    const ERR_TOO_MUCH_TRANSACTION_NUM     = 20001;
}