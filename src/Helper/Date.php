<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Helper;

/**
 * 日期时间数据助手类
 */
class Date
{
    /**
     * 格式化时间
     * @param  integer $time 时间戳
     * @return string
     */
    public static function timeFormat($time)
    {
        if (strpos($time, '.') !== false) {
            list($time, $utime) = explode('.', $time);
        }

        if ($time < 60) {
            $result = sprintf('00:00:%02d', $time);
        } elseif ($time < 3600) {
            $result = sprintf('00:%02d:%02d', floor($time / 60), $time % 60);
        } elseif ($time < 86400) {
            $result = sprintf('%02d:%02d%02d', floor($time / 60), $time % 60);
        } else {
            $result = $time;
        }

        return isset($utime) ? sprintf('%s %s', $result, $utime) : $result;
    }
}