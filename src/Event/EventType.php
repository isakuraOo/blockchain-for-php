<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Event;

/**
 * 事件类型类
 */
class EventType
{
    // 其它节点创建新区块成功
    const EVENT_OTHER_NODE_CREATE_BLOCK_SUCC        = 1001;
    // 其它节点创建新区块签名校验失败
    const EVENT_OTHER_NODE_CRAETE_BLOCK_SIGN_FAIL   = 1002;
    // 其它节点创建新区块哈希值不合法
    const EVENT_OTHER_NODE_CREATE_BLOCK_HASH_FAIL   = 1003;
    // 接收其它节点确认新区块
    const EVENT_OTHER_NODE_CONFIRM_NEW_BLOCK        = 1004;
    // 接收其它节点拒绝新区块
    const EVENT_OTHER_NODE_CANCEL_NEW_BLOCK         = 1005;
    // 有新的交易数据产生
    const EVENT_HAS_NEW_TRANSACTION_DATA            = 1006;
}