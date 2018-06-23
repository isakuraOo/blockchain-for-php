<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Client\SocketClient;
use Joosie\Blockchain\Console\Message\MsgHandler;
use Joosie\Blockchain\BlockchainManager;
use Joosie\Blockchain\Helper\Log;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainClientCommand extends BlockchainCommand
{
    protected $blockchainManager;

    protected $client;

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
                Log::t('Please use the right command.');
                Log::t('Ex: php artisan blockchain [start|restart|stop] {system}');
                break;
        }
    }

    /**
     * 服务启动
     */
    public function start()
    {
        $this->blockchainManager = new BlockchainManager();
        $this->client = $this->blockchainManager->sockClient
            ->getClient()
            ->joinMulticast();

        $this->client->sendto('Hello server,I am client');
    }
}