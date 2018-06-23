<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

interface BlockInterface
{
    /**
     * 获取区块头数据
     * @return array
     */
    public function getHeader();

    /**
     * 获取区块体数据
     * @return array
     */
    public function getBody();

    /**
     * 获取区块完整数据
     * @return array
     */
    public function getBlockData();
}