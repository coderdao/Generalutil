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

    /**
     * 根据条件搜索单个信息
     * @param array $where [ 'whereName'=>'whereValue' ]
     * @param array $column2Select
     * @return array
     */
    public function getInfoByKey( array $where, array $column2Select = [ '*' ] ):array
    {
        $where = array_filter( $where );
        if ( !$where ) { return []; }

        $infoModel = $this->Model->select( $column2Select );
        if ( $where ) {
            foreach ( $where as $k2Where => $v2Where ) {
                $infoModel->where( $k2Where, '=', $v2Where );
            }
        }

        $info = $infoModel->first();
        if ( false === $info ) { return []; }

        // 获取单一字段时,直接返回
        $column2Select_0 = current( $column2Select );
        if ( 1 == count( $column2Select ) && '*' != $column2Select_0 ) {
            return $info->getAttributeValue( $column2Select_0 );
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
    public function getListByKey( $keyName, array $keyId, array $column2Select = [ '*' ], array $where = [] ):array
    {
        $listModel = $this->Model->select( $column2Select );
        if ( $keyName && $keyId ) {
            $listModel = $listModel->whereIn( $keyName, $keyId );
        }

        if ( $where ) {
            foreach ( $where as $k2Where => $v2Where ) {
                $listModel = $listModel->where( $k2Where, $v2Where );
            }
        }

        $list = $listModel->get();
        if ( false === $list ) { return []; }

        return $list->toArray();
    }

    /**
     * 获取 符合条件数据 总量
     * @param $Model
     * @param string $table string 全表总数据
     * @return int
     */
    protected function countSearchTotal( $Model, string $table = '' )
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
    public function getSqlWithBind( $Model )
    {
        $bindings = $Model->getBindings();
        $sql = str_replace( '?', '\'%s\'', $Model->toSql() );
        return sprintf( $sql, ...$bindings );
    }

    /**
     * 单个插入更新
     * @param $fileCategoryRelationArr
     * @param string $tableName
     * @return mixed
     * @throws \Exception
     */
    protected function duplicateKeyInsert( $fileCategoryRelationArr, $tableName = '' )
    {
        $insertColumns = null;
        $updateColumnsKeyArr = $updateColumnsKeyStr = false;
        $tableName = $tableName ?: $this->Model->getTable();

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
        $ret2Return = DB::connection( $this->Model->getConnectionName() )->insert( $duplicateInsertSql, $insertColumnsBindValue );

        return $ret2Return;
    }
}