<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Joosie\Blockchain\Server\SocketServer;
use Joosie\Blockchain\Exceptions\BlockchainServerException;
use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Transaction;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainServerCommand extends BlockchainCommand
{
    protected $serv = null;

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
    protected $signature = 'blockchainServer {action}';

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
        $this->startServ();
        $this->startClient();
    }

    /**
     * 服务端启动
     */
    private function startServ()
    {
        $this->serv = SocketServer::getServer()->set([
            'worker_num'    => env('SW_WORKER_NUM', 4),
            'reactor_num'   => env('SW_REACOTER_NUM', 8),
            'max_request'   => env('SW_MAX_REQUEST', 0),
            'max_conn'      => env('SW_MAX_CONN', 100),
            'backlog'       => env('SW_BACKLOG', 200)
        ])->joinMulticast();

        $this->serv->on('start', [$this->serv, 'onStart']);
        $this->serv->on('packet', [$this->serv, 'onPacket']);
        $this->serv->on('close', [$this->serv, 'onClose']);

        $this->log('Service starting...');
        if (!$this->serv->start()) {
            throw new BlockchainServerException('Service start faild!');
        }
    }

    /**
     * 客户端启动
     */
    private function startClient()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_UDP);
        $socket = $this->client->getSocket();
        $res = socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, $this->multicastOption);
        if (!$res) {
            throw new BlockchainClientException('Set socket options fail!');
        }

        $handler = new BlockchainClientHandler();
        $this->client->connect('127.0.0.1', 9608);
        $this->client->sendto($this->multicastOption['group'], 9608, 'Hello server,I am client');
    }
}