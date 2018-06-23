<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

interface StoreContractInterface
{
    /**
     * 设置数据源
     * @param  string $bucket 数据源名称
     */
    public function from(string $bucket);

    public function select($name);

    public function insert();

    public function count();

    public function delete();

    public function get();

    public function close();

    /**
     * 开启事务
     */
    public function beginTransaction();

    /**
     * 事务提交
     */
    public function execute();
}