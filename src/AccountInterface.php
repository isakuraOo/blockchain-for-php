<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

interface AccountInterface
{
    /**
     * 获取我的账户地址
     */
    public function getMyAccountAddress();

    /**
     * 获取我的账户详情
     */
    public function getMyAccountInfo();

    /**
     * 转账
     * @param  string $toAddress   目标账户地址
     * @param  float  $resourceNum 资源数量
     */
    public function transfer($toAddress, $resourceNum);

    /**
     * 创建账号
     */
    public function create();

}