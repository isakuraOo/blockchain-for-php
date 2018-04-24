<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Illuminate\Console\Command;
use swoole_server;
use Joosie\Blockchain\Exceptions\BlockchainServerException;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainCommand extends Command
{
    protected $serv = null;

    /**
     * 命令格式
     * @var string
     */
    protected $signature = 'blockchain {action}';

    /**
     * 命令简介
     * @var string
     */
    protected $description = 'Blockchain service for laravel';

    /**
     * 命令执行入口
     */
    public function handle()
    {
        $action = $this->argument('action');
        switch ($action) {
            case 'start':
                $this->start();
                break;
            case 'restart':
                # code...
                break;
            case 'stop':
                # code...
                break;
            default:
                $this->log('Please use the right command.');
                $this->log('Ex: php artisan blockchain [start|restart|stop] {system}');
                break;
        }
    }

    /**
     * 服务启动
     */
    public function start()
    {
        $this->serv = new swoole_server('127.0.0.1', 9501);
        $this->serv->set([
            'worker_num'    => env('SW_WORKER_NUM', 4),
            'reactor_num'   => env('SW_REACOTER_NUM', 8),
            'max_request'   => env('SW_MAX_REQUEST', 0),
            'max_conn'      => env('SW_MAX_CONN', 100),
            'backlog'       => env('SW_BACKLOG', 200)
        ]);
        $handler = new BlockchainHandler();
        $this->serv->on('start', [$handler, 'onStart']);
        $this->serv->on('connect', [$handler, 'onConnect']);
        $this->serv->on('receive', [$handler, 'onReceive']);
        $this->serv->on('close', [$handler, 'onClose']);

        $this->log('Service starting...');
        if (!$this->serv->start()) {
            throw new BlockchainServerException('Service start faild!');
        }
    }

    /**
     * 日志输出
     * @param  string $content 输出内容
     * @param  string $lv      内容级别[INFO|SUCCESS|ERROR]
     */
    private function log($content, $lv = 'INFO')
    {
        if ($lv === 'INFO')
            echo sprintf('%s' . PHP_EOL, $content);
        elseif ($lv === 'ERROR')
            echo sprintf("\033[31m%s\033[0m" . PHP_EOL, $content);
        elseif ($lv === 'SUCCESS')
            echo sprintf("\033[32m%s\033[0m" . PHP_EOL, $content);
        else
            echo sprintf('%s' . PHP_EOL, $content);
    }
}