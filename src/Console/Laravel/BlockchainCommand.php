<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Console\Laravel;

use Illuminate\Console\Command;

/**
 * 基于 Laravel 的命令行处理基类
 */
class BlockchainCommand extends Command
{
    const LOG_TYPE_INFO     = 1;
    const LOG_TYPE_ERROR    = 2;
    const LOG_TYPE_SUCCESS  = 3;

    /**
     * 日志输出
     * @param  string $content 输出内容
     * @param  string $lv      内容级别[INFO|SUCCESS|ERROR]
     */
    protected function log($content, $lv = self::LOG_TYPE_INFO)
    {
        if ($lv === self::LOG_TYPE_INFO)
            echo sprintf('%s' . PHP_EOL, $content);
        elseif ($lv === self::LOG_TYPE_ERROR)
            echo sprintf("\033[31m%s\033[0m" . PHP_EOL, $content);
        elseif ($lv === self::LOG_TYPE_SUCCESS)
            echo sprintf("\033[32m%s\033[0m" . PHP_EOL, $content);
        else
            echo sprintf('%s' . PHP_EOL, $content);
    }
}