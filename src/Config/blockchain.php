<?php
/**
 * @author iSakura <i@joosie.cn>
 */
return [
    // 主链版本号，在生成账户地址的时候需要用到版本号进行哈希计算
    'version'           => '00',

    // 密钥文件路径
    'privateKeyPath'    => '',
    'publicKeyPath'     => '',

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
        'sockClient'    => \Joosie\Blockchain\Client\SocketClient::class,
        'sockServer'    => \Joosie\Blockchain\Server\SocketServer::class,
        'store'         => \Joosie\Blockchain\Stores\storeManager::class,
        'account'       => \Joosie\Blockchain\Account::class,
        'accountResource' => \Joosie\Blockchain\AccountResource::class,
        'block'         => \Joosie\Blockchain\Stores\Block::class,
    ],
];