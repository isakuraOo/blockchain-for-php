<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Illuminate\Console\Command;
use swoole_client;
use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Transaction;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainClientCommand extends Command
{
    protected $client = null;

    /**
     * 组播设置
     * @var array
     */
    protected $multicastOption = ['group' => '233.233.233.233', 'interface' => 'en0'];

    /**
     * 命令格式
     * @var string
     */
    protected $signature = 'blockchainClient {action}';

    /**
     * 命令简介
     * @var string
     */
    protected $description = 'Blockchain client for laravel';

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
        $this->client = new swoole_client(SWOOLE_SOCK_UDP);
        $this->client->connect('0.0.0.0', 9608);
        $socket = $this->client->getSocket();
        $res = socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, $this->multicastOption);
        if (!$res) {
            throw new BlockchainClientException('Set socket options fail!');
        }

        $handler = new BlockchainClientHandler();
        $this->client->sendto($this->multicastOption['group'], 9608, 'Hello server,I am client');
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