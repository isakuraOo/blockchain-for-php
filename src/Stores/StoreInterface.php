<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

interface StoreInterface
{
    public function connect();

    public function close();
}