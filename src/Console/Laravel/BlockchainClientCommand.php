<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Client\SocketClient;
use Joosie\Blockchain\Console\Message\MsgHandler;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainClientCommand extends BlockchainCommand
{
    protected $client = null;

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
        $this->client = SocketClient::getClient()->joinMulticast();
        $msgHandler = new MsgHandler('Hello server,I am client');
        $this->client->sendto($msgHandler);
    }
}