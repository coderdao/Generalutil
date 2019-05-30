<?php
/**
 * Function:
 * Description:
 * Abo 2019/5/30 22:05
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;


use Abo\Generalutil\V1\Exceptions\ApiException;
use Abo\Generalutil\V1\Exceptions\PageException;

class ExceptionHandleUtil
{
    public static function handle( \Exception $exception )
    {
        $code = $exception->getCode();

        /* 错误页面 */
        if ( $exception instanceof PageException ) { // todo view 加载模板路径处理
            return response()->view('errors.errorMsg', ['msg'=>$exception->getMessage()], $code);
            exit();
        }

        if ( $exception instanceof ApiException ) {
            return ResponseUtil::error()->message( $exception->getMessage() )->json();
        }
    }
}