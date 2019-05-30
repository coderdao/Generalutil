<?php
/**
 * Function:
 * Description:
 * Abo 2019/5/30 22:05
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;


class ExceptionHandleUtil
{
    public static function handle( \Exception $exception )
    {
        /* 错误页面 */
        if ($exception instanceof PageException) {
            $code = $exception->getCode();
            if (view()->exists('errors.errorMsg')) {
                return response()->view('errors.errorMsg', ['msg'=>$exception->getMessage()], $code);
            }
        }
    }
}