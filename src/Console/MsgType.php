<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console;

/**
 * 消息类型类
 */
class MsgType
{
    // 新工作节点加入
    const TYPE_NEW_NODE_JOIN    = 1;
    // 新区块创建
    const TYPE_NEW_BLOCK_CREATE = 2;
}