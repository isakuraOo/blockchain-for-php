<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

interface AccountResourceInterface
{
    /**
     * 获取资产详细内容
     */
    public function getInfo();

    /**
     * 增加资产
     * @param float $amount  资产变动数量
     */
    public function add($amount);

    /**
     * 减少资产
     * @param  float $amount 资产变动数量
     */
    public function less($amount);
}