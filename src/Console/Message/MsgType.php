<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Message;

/**
 * 消息类型类
 */
class MsgType
{
    // 普通广播消息
    const TYPE_COMMON           = 0;
    // 新工作节点加入
    const TYPE_NEW_NODE_JOIN    = 1;
    // 新区块创建
    const TYPE_NEW_BLOCK_CREATE = 2;
    // 单条数据写入
    const TYPE_ADD_ONE_DATA     = 3;
    // 批量数据写入
    const TYPE_BATCH_ADD_DATA   = 4;
}