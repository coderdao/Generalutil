<?php
/**
 * Function: 代码性能分析工具
 * Abo 2019/7/4 18:11
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;

class AnalysisUtil
{
    static $start_time;
    static $end_time;
    static $start_memory;
    static $end_memory;

    public static function start()
    {
        self::$start_memory = memory_get_usage();  //单位为 byte(s)
        self::$start_time = microtime( true );

        echo "\r\n<br/>" . 'Start @'
            . self::$start_time . '(' . self::$start_memory . ')|------->';
    }



    public static function end()
    {
        self::$end_time = microtime( true );
        self::$end_memory = memory_get_usage();

        echo 'End @'.self::$end_time.'('.self::$end_memory.') :';
        echo '|======= 共耗时：'.(self::$end_time-self::$start_time).'ms，共用内存：'.(self::$end_memory-self::$start_memory);
    }

    /**
     * 使用方法
    //消除t类首次加载的影响
    t::start();
    t::end();

    t::start();
    $str = "我来到你的城市走过你来时的路，想象着没我的日子你是怎样的孤独";
    t::end();

    显示结果：
    Start @1447408386.0921(242528)|------->End @1447408386.0922(242720) :|======= 共耗时：3.6001205444336E-5，共用内存：192
    Start @1447408386.0922(242720)|------->End @1447408386.0922(242856) :|======= 共耗时：5.0067901611328E-6，共用内存：136
     */

}