<?php
/**
 * Function: 大量数据处理工具
 * Description:
 * Abo 2019/7/19 14:22
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;

class BigDataUtil
{
    /**
     * 大型数据切割处理器
     * @param int $total
     * @param \Closure $closure( $page, $size ) 数据分页回调函数
     * @param int $size
     * @return int|mixed
     */
    public static function dataChunk( int $total, \Closure $closure, int $size = 500  )
    {
        if ( $size < 1 ) { return false; }

        // 分批添加
        $insertNum = 0;
        $pageNum = ceil( $total / $size );

        // 分页请求数据
        for ( $i2Count = 1; $i2Count <= $pageNum; $i2Count++ ) {
            $insertNum += call_user_func( $closure, $i2Count, $size );
        }

        return $insertNum;
    }
}