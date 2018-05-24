<?php
/**
 * @author iSakura <i@joosie.cn>
 */
return [
    // 存储方式 redis|memcached|file|mysql 默认 redis
    'storage'   => 'redis',
    // 服务
    'services'  => [
        'sockClient'    => \Joosie\Blockchain\Client\SocketClient::class,
        'sockServer'    => \Joosie\Blockchain\Server\SocketServer::class,
        'account'       => \Joosie\Blockchain\Account::class,
        'accountResource' => \Joosie\Blockchain\AccountResource::class,
    ],
];