<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Storage;

/**
* 区块数据类
*/
class Block
{
    /**
     * 区块头
     * @var array
     */
    protected $header = [
        'nonce' => '',
        'version' => '',
        'timestamp' => 0,
        'prevHash' => '',
        'hash' => '',
        'dataNum' => 0,
    ];

    /**
     * 区块体
     * @var array
     */
    protected $body = [];

    /**
     * 区块数据
     * @var null
     */
    protected $data = null;
}