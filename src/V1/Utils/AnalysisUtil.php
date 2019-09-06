<?php
/**
 * Function: 代码性能分析工具
 * Abo 2019/7/4 18:11
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;

class AnalysisUtil
{
    private static $start_time;
    private static $start_memory;

    private static $end_time;
    private static $end_memory;

    private static $i2Count = 0;

    public static function start()
    {
        self::$i2Count++;

        self::$start_memory = memory_get_usage();  //单位为 byte(s)
        self::$start_time = microtime( true );

        // echo "\r\n<br/>" . 'Start @'. self::$start_time . '(' . self::$start_memory . ')|------->';
    }

    public static function end()
    {
        self::$end_time = microtime( true );
        self::$end_memory = memory_get_usage();

        /*
        'Start '. self::$start_time . '(' . self::$start_memory . ')|------->'
            .'End '.self::$end_time.'('.self::$end_memory.') :'
            .
        */
        $ret = 'use_time_ _'.(self::$end_time-self::$start_time).'ms_ _use_memory_ _'
            .(self::$end_memory-self::$start_memory);

        // echo 'debug-' . self::$i2Count . '~' . $ret . "\r\n";
        // setcookie('debug-'.self::$i2Count, $ret);
        LogUtil::info( 'debug-' . self::$i2Count . '~' . $ret, '', 'AnalysisUtil' );
        //response()->header( 'debug-', $ret );
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