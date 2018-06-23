<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Joosie\Blockchain\Server\SocketServer;
use Joosie\Blockchain\Client\SocketClient;
use Joosie\Blockchain\Exceptions\BlockchainServerException;
use Joosie\Blockchain\Exceptions\BlockchainClientException;
use Joosie\Blockchain\Console\Message\MsgHandler;
use Joosie\Blockchain\BlockchainManager;
use Joosie\Blockchain\ConfigManager;
use Joosie\Blockchain\Stores\StoreManager;
use Joosie\Blockchain\Helper\Log;

/**
* 基于 Laravel 的命令行处理类
*/
class BlockchainServerCommand extends BlockchainCommand
{
    /**
     * 区块链扩展服务主类实例
     * @var \Joosie\Blockchain\BlockchainManager
     */
    protected $blockchainManager;

    /**
     * Socket 服务端实例
     * @var \swoole_server
     */
    protected $serv;

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
        $config = [
            'privateKeyPath'    => env('PRIVATE_KEY_PATH'),
            'publicKeyPath'     => env('PUBLIC_KEY_PATH'),
            'difficulty'        => env('BLOCK_DIFFICULTY')
        ];
        $configManager = new ConfigManager($config);
        
        $blockchain = new BlockchainManager($configManager);
        $blockchain->container->set('store', function() use ($blockchain) {
            return StoreManager::getInstance($blockchain)->connect();
        });
        $this->blockchainManager = $blockchain;

        // 检查是否有自己的钱包账号，没有的话询问用户是否创建一个
        if (
            !$this->blockchainManager->account->hasAccount()
            && $this->confirm('You don\'t have a wallet account yet! Do you need to generate one?')
        ) {
            $account = $this->blockchainManager->account->create();
            $account->savePrivateKeyToFile();
            $account->savePublicKeyToFile();
            $accountInfo = $account->getMyAccountInfo();
            Log::t(
                sprintf("Your account data: \n%s", json_encode($accountInfo, JSON_PRETTY_PRINT)), Log::LOG_TYPE_SUCCESS
            );
        }
        
        $this->startServer();
        // $this->startClient();
    }

    /**
     * 服务端启动
     */
    private function startServer()
    {
        $this->serv = $this->blockchainManager->sockServer
            ->getServer()
            ->set([
                'worker_num'    => env('SW_WORKER_NUM', 4),
                'reactor_num'   => env('SW_REACOTER_NUM', 8),
                'max_request'   => env('SW_MAX_REQUEST', 0),
                'max_conn'      => env('SW_MAX_CONN', 100),
                'backlog'       => env('SW_BACKLOG', 200)
            ])->joinMulticast();

        $this->serv->on('start', [$this->serv, 'onStart']);
        $this->serv->on('packet', [$this->serv, 'onPacket']);
        $this->serv->on('close', [$this->serv, 'onClose']);

        Log::t('Service starting...');
        if (!$this->serv->start()) {
            throw new BlockchainServerException('Service start faild!');
        }
    }

    /**
     * 客户端启动
     */
    // private function startClient()
    // {
    //     $this->client = $this->blockchainManager->sockClient
    //         ->getClient()
    //         ->joinMulticast();
        
    //     $this->client->sendto('Hello server,I am client');
    // }
}