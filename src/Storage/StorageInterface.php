<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Storage;

interface StorageInterface
{
    /**
     * 根据账号地址查找账号数据
     * @param String $account 账号地址
     */
    public function findAccountInfo($account);

    /**
     * 查找所有链上数据
     */
    public function findAll();

    /**
     * 根据账号查找账号资源余额
     * @param String $account 账号地址
     */
    public function findAccountBalance($account);

    /**
     * 根据账号查找账号交易记录
     * @param  mixed $condition 根据条件查找交易记录
     */
    public function findTransLogByCondition($condition);
}