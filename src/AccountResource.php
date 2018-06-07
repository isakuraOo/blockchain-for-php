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
     * 默认资产精度
     */
    const DEFAULT_BALANCE_SCALE = 10;

    /**
     * 账户资产余额
     * @var float
     */
    protected $balance = 0.0000000000;

    /**
     * 账户
     * @var null
     */
    protected $account = null;

    /**
     * 初始化处理
     * @return void
     */
    protected function init()
    {
        $config = $this->blockchainManager->config;
        // 设置资产精度
        bcscale(isset($config['balanceScale'])
            ? $config['balanceScale'] : self::DEFAULT_BALANCE_SCALE);
    }

    public function getInfo()
    {
        return $this;
    }

    public function add($amount)
    {

    }

    public function less($amount)
    {

    }
}