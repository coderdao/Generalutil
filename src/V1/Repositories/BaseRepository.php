<?php
/**
 * Description: 基础数据模型(Model)操作类
 * Abo 2018/1/2 18:05
 * Email: abo2013@foxmail.com
 *
 * @method getInfoByKey( array $where, array $column2Select )                           根据条件搜索单个信息
 * @method getListByKey( $keyName, array $keyId, array $column2Select, array $where )   根据字段,获取列表信息
 * @method countSearchTotal( $Model, $table = '' )                                      获取 符合条件数据 总量
 * @method duplicateKeyInsert( $fileCategoryRelationArr, $tableName = '' )              单个插入更新
 * @method getSqlWithBind( $Model )         获取带参数sql
 */

namespace Abo\Generalutil\V1\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseRepository
 * @package Abo\Generalutil\V1\Repositories
 */
class BaseRepository
{
    /** @var $Model \Illuminate\Database\Query\Builder */
    protected $Model;

    public function __construct( Model $Model )
    {
        $this->Model = $Model;
    }

    /**
     * 根据条件搜索单个信息
     * @param array|Eloquent $where [ '!whereName'=>'whereValue' ]
     * @param array $column2Select
     * @return array|mixed
     */
    public function getInfoByKey( $where, array $column2Select = [ '*' ] )
    {
        // 条件
        if ( $where instanceof \Eloquent ) {
            $this->Model = $where;
        }
        if ( is_array( $where ) ) {
            $this->Model = $this->handleWhereParam( $this->Model, $where );
        }
        $infoModel = $this->Model->select( $column2Select );

        $info = $infoModel->first();
        if ( !$info ) { return []; }
        $info = $info->toArray();

        // 获取单一字段时,直接返回
        $column2Select_0 = current( $column2Select );
        if ( 1 == count( $column2Select ) && '*' != $column2Select_0 ) {
            return current( $info );
        }

        return $info;
    }

    /**
     * 根据字段,获取列表信息
     * @param $keyName string
     * @param array $keyId
     * @param array $column2Select
     * @param array|Eloquent $where [ '!whereName'=>'whereValue' ]
     * @param array $paginate [ 'pageNow', 'pageSize', 'order'/[ 'order' => 'desc' ] ]
     * @return array
     */
    public function getListByKey( array $column2Select = [ '*' ], $where, array $paginate = [] ):array
    {
        // 条件
        if ( $where instanceof \Eloquent ) {
            $this->Model = $where;
        }
        if ( is_array( $where ) ) {
            $this->Model = $this->handleWhereParam( $this->Model, $where );
        }
        $listModel = $this->Model->select( $column2Select );

        // 分页/排序
        $defaultPaginate = [ 0, 0, 0 ];
        $paginate += $defaultPaginate;
        list( $page, $pageNum, $orderBy ) = $paginate;
        if ( $page && $pageNum
            && ( is_int( $page ) && is_int( $pageNum ) )
        ) {
            $listModel = $listModel->forPage( $page, $pageNum );
        }

        if ( is_string( $orderBy ) && $orderBy ) {
            $listModel = $listModel->orderByDesc( $orderBy );
        }elseif ( is_array( $orderBy ) && $orderBy ) {
            foreach ( $orderBy as $k2OrderBy => $v2OrderBy ) {
                $listModel = $listModel->orderBy( $k2OrderBy, $v2OrderBy );
            }
        }

        // 结果 - 获取单一字段时,直接返回
        $column2Select_0 = current( $column2Select );
        if ( 1 == count( $column2Select ) && '*' != $column2Select_0 ) {
            $list = $listModel->pluck( $column2Select_0 );
        }else{
            $list = $listModel->get();
        }

        if ( !$list ) { return []; }
        return $list->toArray();
    }

    /**
     * 获取 符合条件数据 总量
     * @param $Model
     * @param string $table string 全表总数据
     * @return int
     */
    public static function countSearchTotal( $Model, string $table = '' )
    {
        if ( $table ){
            $countSql = 'SELECT COUNT(1) as num FROM '.$table;
        }else{
            $searchSql = self::getSqlWithBind( $Model );
            $countSql = 'SELECT COUNT(1) as num FROM ('.$searchSql.') AS t';
        }

        $count = DB::connection( $Model->getConnectionName() )->select( $countSql );

        $ret = json_decode( json_encode( $count[0] ), true );
        return !array_key_exists( 'num', $ret ) ? 0:$ret['num'];
    }

    /**
     * 获取带参数sql
     * @param $Model
     * @return string
     */
    public static function getSqlWithBind( $Model )
    {
        $bindings = $Model->getBindings();
        $sql = str_replace( '?', '\'%s\'', $Model->toSql() );
        return sprintf( $sql, ...$bindings );
    }

    /**
     * 单个插入更新
     * @param array $fileCategoryRelationArr
     * @param string $tableName
     * @return mixed
     * @throws \Exception
     */
    public static function duplicateKeyInsert( array $fileCategoryRelationArr, string $tableName = '', string $connection = '' )
    {
        $insertColumns = null;
        $updateColumnsKeyArr = $updateColumnsKeyStr = false;

        // 添加部分绑定
        $insertColumns = array_keys( $fileCategoryRelationArr );
        $insertColumnsBindValue = array_values( $fileCategoryRelationArr );

        $insertColumnsQuestionStr = implode( ',', array_fill( 0, count( $fileCategoryRelationArr ), '?' ) );

        // 更新部分数据绑定
        foreach ( $fileCategoryRelationArr as $k2cate => $v2cate ) {
            $insertColumnsKeyStr[] = "`{$k2cate}`";
            $updateColumnsKeyArr[] = "`{$k2cate}`='{$v2cate}'";
        }

        $insertColumnsKeyStr = implode( ',', $insertColumnsKeyStr );
        $updateColumnsKeyStr = implode( ',', $updateColumnsKeyArr );

        $duplicateInsertSql = "insert into {$tableName} ( {$insertColumnsKeyStr} ) "
            . "values ( {$insertColumnsQuestionStr} )  ON DUPLICATE KEY UPDATE {$updateColumnsKeyStr};";

        if ( $connection ) {
            $ret2Return = DB::connection( $connection )->insert( $duplicateInsertSql, $insertColumnsBindValue );
        }else{
            $ret2Return = DB::insert( $duplicateInsertSql, $insertColumnsBindValue );
        }


        return $ret2Return;
    }

    /**
     * 多个个插入更新
     * @param array $fileCategoryRelationArr [ [ 'id' => 1 ] ]
     * @param string $tableName
     * @return mixed
     * @throws \Exception
     */
    public static function duplicateKeyInsertArray( array $fileCategoryRelationArr, string $tableName = '', string $connection = '' )
    {
        if ( empty( $fileCategoryRelationArr ) ) { return false; }
        foreach ( $fileCategoryRelationArr as $v2Data ) {
            $updateColumnsKeyArr = $insertColumnsKey = $insertColumnsBindValue = [];

            // 更新部分数据绑定
            foreach ( $v2Data as $k2cate => $v2cate ) {
                $updateColumnsKeyArr[] = "{$k2cate}='{$v2cate}'";
                $insertColumnsKey[] = "`{$k2cate}`";
                $insertColumnsBindValue[] = "'{$v2cate}'";
            }
            $updateColumnsKeyStr = implode( ',', $updateColumnsKeyArr );
            $insertColumnsKeyStr = implode( ',', $insertColumnsKey );
            $insertColumnsQuestionStr = implode( ',', $insertColumnsBindValue );

            $duplicateInsertSqlArray[] = "insert into {$tableName} ( {$insertColumnsKeyStr} ) "
                . "values ( {$insertColumnsQuestionStr} )  ON DUPLICATE KEY UPDATE {$updateColumnsKeyStr};";
        }

        if ( !$duplicateInsertSqlArray ) { return false; }
        if ( $connection ) {
            foreach ( $duplicateInsertSqlArray as $duplicateInsertSql ) {
                $ret2Return = DB::connection( $connection )->insert( $duplicateInsertSql );
            }
        }else{
            foreach ( $duplicateInsertSqlArray as $duplicateInsertSql ) {
                $ret2Return = DB::insert( $duplicateInsertSql );
            }
        }

        return $ret2Return;
    }

    /** 添加数组处理 */
    private function handleWhereParam( $Model, array $where )
    {
        if ( !$Model || !$where ) { return $Model; }

        foreach ( $where as $k2Where => $v2Where ) {
            // 数组
            if ( is_array( $v2Where ) ) {
                if ( 0 === strpos( $k2Where, '!' ) ) {
                    $Model = $Model->whereNotIn( ltrim( $k2Where, '!' ), $v2Where );
                    continue;
                }

                $Model = $Model->whereIn( $k2Where, $v2Where );
                continue;
            }

            // 字符串||数字
            if ( 0 === strpos( $k2Where, '!' ) ) {
                $Model = $Model->where( ltrim( $k2Where, '!' ), '!=', $v2Where );
                continue;
            }

            $Model = $Model->where( $k2Where, '=', $v2Where );
        }

        return $Model;
    }


    /**
     * 添加/更新
     * @param array $saveData
     * @param int $keyValue 一般是id,主要取决 $keyName 所指字段
     * @param string $keyName
     * @return int
     */
    public function save( array $saveData, int $keyValue, $keyName = 'id' ):int
    {
        if ( $keyValue ) {
            $ret = $this->Model->where( $keyName, '=', $keyValue )->update( $saveData );
        }else{
            $ret = $this->Model->insertGetId( $saveData );
        }

        return $ret;
    }


    /** 删除方法 */
    public function delete( $id, $idName = 'id' )
    {
        if ( is_array( $id ) ) {
            $ret = $this->Model->whereIn( $idName, $id )->delete();
        }else{
            $ret = $this->Model->where( $idName, '=', $id )->delete();
        }

        return $ret;
    }
}