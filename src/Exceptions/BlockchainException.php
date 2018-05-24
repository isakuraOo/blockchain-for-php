<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Exceptions;

/**
* 区块链主异常类
*/
class BlockchainException extends \Exception
{
    const CODE = 500;
    
    function __construct($message, $code = self::CODE)
    {
        parent::__construct($message, $code);
    }
}