<?php
/**
 * Function: 数组工具类方法
 * @method randonString( int $length = 8 )  随机字母字符串
 * @method randonNum( int $length = 6 )     随机数组字符串
 */

namespace Abo\Generalutil\Utils;


class ArrayUtil
{
// 二维数组 => 树形结构 id:主键, pid:父级id
    public function array2Tree( array $rows, $id='id', $pid='pid' ) {
        $items = array();

        foreach ( $rows as $row ) {
            $row[ 'level' ] = 1; // 初始 深度/层数
            $items[ $row[ $id ] ] = $row;
        }
        foreach ($items as $item) {
            $item[ $pid ] != 0 && $items[ $item[ $id ] ][ 'level' ] += $items[ $item[ $pid ] ][ 'level' ]; // 计算 深度/层数
            $items[ $item[ $pid ] ][ 'son' ][ $item[ $id ] ] = &$items[ $item[ $id ] ];
        }

        return isset( $items[ 0 ][ 'son' ] ) ? $items[ 0 ][ 'son' ] : array();
    }

    // 树形结构 => 二维数组( 带层级排序 )
    public function tree2Array( array $tree ) {
        static $arr = array();

        foreach( $tree as $val ) {
            $tem = $val;
            unset( $tem[ 'son' ] );
            $arr[] = $tem;

            if( isset( $val[ 'son' ] ) && !empty( $val['son'] ) ) {
                self::tree2Array( $val['son'] );
            }
        }
        return $arr;
    }
}