<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainAccountException;
use Joosie\Blockchain\Providers\Service;

/**
 * 账户资产基类
 * 正式上链使用需要根据实际业务实现该类的方法
 */
class AccountResource extends Service implements AccountResourceInterface
{
    /**
     * 账户余额
     * @var float
     */
    protected $balance = 0.00;

    /**
     * 账户
     * @var null
     */
    protected $account = null;

    public function getInfo()
    {

    }

    public function add($amount)
    {

    }

    public function less($amount)
    {

    }
}