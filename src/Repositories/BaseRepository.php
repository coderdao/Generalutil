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

namespace Abo\Generalutil\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseRepository
 * @package Abo\Generalutil\Repositories
 */
class BaseRepository
{
    protected $Model;

    public function __construct( Model $Model )
    {
        $this->Model = $Model;
    }

    /** 根据条件搜索单个信息 @param array $where [ 'whereName'=>'whereValue' ] */
    public function getInfoByKey( array $where, array $column2Select = [ '*' ] )
    {
        $where = array_filter( $where );
        if ( !$where ) { return []; }

        $infoModel = $this->Model->select( $column2Select );
        $infoModel = $this->handleWhereParam( $infoModel, $where );

        $info = $infoModel->first();
        if ( false === $info ) { return []; }
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
     * @param array $where [ 'whereName'=>'whereValue' ]
     * @return array
     */
    public function getListByKey( array $column2Select = [ '*' ], array $where = [], int $page = 0, int $pageNum = 0 ):array
    {
        $listModel = $this->Model->select( $column2Select );
        $listModel = $this->handleWhereParam( $listModel, $where );

        if ( $page && $pageNum ) {
            $listModel = $listModel->forPage( $page, $pageNum );
        }

        // 获取单一字段时,直接返回
        $column2Select_0 = current( $column2Select );
        if ( 1 == count( $column2Select ) && '*' != $column2Select_0 ) {
            $list = $listModel->pluck( $column2Select_0 );
        }else{
            $list = $listModel->get();
        }

        if ( false === $list ) { return []; }

        return $list->toArray();
    }

    /**
     * 获取 符合条件数据 总量
     * @param $Model
     * @param string $table string 全表总数据
     * @return int
     */
    public function countSearchTotal( $Model, string $table = '' )
    {
        if ( $table ){
            $countSql = 'SELECT COUNT(1) as num FROM '.$table;
        }else{
            $searchSql = $this->getSqlWithBind( $Model );
            $countSql = 'SELECT COUNT(1) as num FROM ('.$searchSql.') AS t';
        }

        $count = DB::connection( $this->Model->getConnectionName() )->select( $countSql );

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
        $insertColumnsKeyStr = implode( ',', $insertColumns );

        // 更新部分数据绑定
        foreach ( $fileCategoryRelationArr as $k2cate => $v2cate ) {
            $updateColumnsKeyArr[] = "{$k2cate}='{$v2cate}'";
        }

        $updateColumnsKeyStr = implode( ',', $updateColumnsKeyArr );

        $duplicateInsertSql = "insert into {$tableName} ( {$insertColumnsKeyStr} ) "
            . "values ( {$insertColumnsQuestionStr} )  ON DUPLICATE KEY UPDATE {$updateColumnsKeyStr}";

        if ( $connection ) {
            $ret2Return = DB::connection( $connection )->insert( $duplicateInsertSql, $insertColumnsBindValue );
        }else{
            $ret2Return = DB::insert( $duplicateInsertSql, $insertColumnsBindValue );
        }


        return $ret2Return;
    }

    /** 添加数组处理 */
    private function handleWhereParam( $Model, array $where )
    {
        if ( !$Model || !$where ) { return $Model; }

        foreach ( $where as $k2Where => $v2Where ) {
            if ( isset( $v2Where[ 'op' ] ) ) {
                $Model = $Model->where( $k2Where, strval( $v2Where[ 'op' ] ), $v2Where );
                continue;
            }

            $Model = $Model->where( $k2Where, '=', $v2Where );
        }

        return $Model;
    }
}