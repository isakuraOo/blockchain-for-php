<?php
/**
 * @author iSakura <i@joosie.cn>
 */
return [
    // 主链版本号，在生成账户地址的时候需要用到版本号进行哈希计算
    'version'           => '00',
    // 区块生成难度系数
    'difficulty'        => '',

    // 密钥文件路径
    'privateKeyPath'    => dirname(dirname(__FILE__)) . '/cert/blockchain_account.cert',
    'publicKeyPath'     => dirname(dirname(__FILE__)) . '/cert/blockchain_account_pub.cert',

    // 存储方式 redis|memcached|file|mysql 默认 redis
    'store'             => 'redis',
    // 存储服务配置信息
    'storeConfig' => [
        // Redis 连接服务配置参数
        'redis' => [
            'host'  => '127.0.0.1',
            'port'  => '6379',
            'auth'  => '',
        ],
    ],

    // 扩展提供服务
    'services' => [
        // Socket 客户端处理服务
        'sockClient'    => \Joosie\Blockchain\Client\SocketClient::class,
        // Socket 服务端处理服务
        'sockServer'    => \Joosie\Blockchain\Server\SocketServer::class,
        // 存储服务
        'store'         => \Joosie\Blockchain\Stores\storeManager::class,
        // 账户处理服务
        'account'       => \Joosie\Blockchain\Account::class,
        // 账户资源处理服务
        'accountResource' => \Joosie\Blockchain\AccountResource::class,
        // 区块数据处理服务
        'block'         => \Joosie\Blockchain\Stores\Block::class,
        // 事件处理服务
        'event'         => \Joosie\Blockchain\Event\EventManager::class,
        // 共识机制服务
        'consensus'     => \Joosie\Blockchain\Validation\Consensus::class,
    ],
];