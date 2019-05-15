<?php
/**
 * Function: 为了躲开 undefined index
 * Description: 当开启 error_reporting = E_ALL~E_NOTICE时,躲开保存,同时得到 数据传输类
 * Abo 2019/1/18 15:22
 * Email: abo2013@foxmail.com
 */

namespace Abo\Fasterapi\Dto\BaseDto;

use Illuminate\Http\Request;

class RequestDto
{
    const DEFAULT_VALUE = '';
    protected $urlParams, $request;

    public function __construct()
    {
        $request = \request();
        $this->request = $request;
        $this->urlParams = $request->all();
    }

    /** 获取请求参数 */
    public function request( string $key = '', $default = false, string $filter = '' )
    {
        if ( !$key ) { return $this->urlParams; }
        $ret2Default = $default ?:self::DEFAULT_VALUE;

        if ( !isset( $this->urlParams[$key] ) ) {
            return $ret2Default;
        }

        $ret2Default = $this->urlParams[$key];
        if ( $filter && function_exists( $filter ) ) {
            $ret2Default = $filter( $this->urlParams[$key] );
        }
        return $ret2Default; // array_key_exists( $key, $this->urlParams )
    }


    /** 是否存在请求参数 */
    public function hasKey( string $key = '' )
    {
        return isset( $key, $this->urlParams );
    }

    /** 获取 Request 请求对象 @return Request */
    public function getRequest()
    {
        return $this->request;
    }

    public function setUrlParam( string $key, $value = '' )
    {
        $this->urlParams[ $key ] = $value;
    }

    /** 获取当前页数 */
    public function getPageNow( int $pageNow = 1 )
    {
        if ( $this->hasKey( 'pageNow' ) ) {
            $pageNow = $this->request('pageNow', $pageNow, 'intval');
        }
        if ( $this->hasKey( 'page' ) ) {
            $pageNow = $this->request( 'page', $pageNow, 'intval');
        }
        if ( $this->hasKey( 'p' ) ) {
            $pageNow = $this->request( 'p', $pageNow, 'intval');
        }

        return ( $pageNow >= 1 ?:1 );
    }

    /** 获取当前每页数量 */
    public function getPageSize( int $pageSize = 20 )
    {
        if ( $this->hasKey( 'pageSize' ) ) {
            $pageSize = $this->request('pageSize', $pageSize, 'intval');
        }
        if ( $this->hasKey( 'page' ) ) {
            $pageSize = $this->request( 'pagesize', $pageSize, 'intval');
        }

        return ( $pageSize >= 20 ?:20 );
    }
}