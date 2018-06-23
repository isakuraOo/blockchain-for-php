<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Event;

use Joosie\Blockchain\Providers\Service;
use Joosie\Blockchain\Exceptions\BlockchainEventException;

/**
 * 事件管理者类
 */
class EventManager extends Service
{
    protected $events = [];

    /**
     * 添加监听的事件
     * @param  string $event  事件名
     * @param  mixed  $handle 事件处理
     * @return self
     */
    public function listen($event, $handle)
    {
        if (!is_callable($handle) && !is_array($handle)) {
            throw new BlockchainEventException('Invalid event handle for %s', $event);
        }

        $this->events[$event] = $handle;
        return $this;
    }

    /**
     * 事件触发
     * @param  string $event 事件名
     * @return void
     */
    public function trigger($event)
    {
        if (!isset($this->events[$event])) {
            throw new BlockchainEventException(sprintf('Undefined event: [%s]', $event));
        }

        $eventHandle = $this->events[$event];
        if (is_callable($$eventHandle)) {
            return call_user_func($eventHandle, $this->blockchainManager);
        } elseif (is_array($eventHandle) && count($eventHandle) >= 2) {
            list($handler, $handle) = $eventHandle;
            if (!method_exists($handler, $handle)) {
                throw new BlockchainEventException(
                    sprintf('Not found method [%s] in %s', $handle, get_class($handler))
                );
            }

            return $handler->{$handle}();
        } else {
            throw new BlockchainEventException('Invalid event handle for %s', $event);
        }
    }
}