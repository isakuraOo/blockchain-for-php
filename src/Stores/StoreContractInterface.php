<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

interface StoreContractInterface
{
    public function from(string $bucket);

    public function select($name);

    public function insert();

    public function count();

    public function delete();

    public function get();

    public function close();
}