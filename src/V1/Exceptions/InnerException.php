<?php
/**
 * Description: 内部错误,不对外公开,仅记录日志
 * Abo 2018/12/29 16:07
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Exceptions;

class InnerException extends \Exception
{
    public function __construct($code = 500, $message = '') {
        parent::__construct();
        // 索引为数字时，不能用array_merge，否则合并后会重新索引。

        $this->code = empty($code) ? 500 : $code;
        $this->message = $message ? $message : '系统异常,请刷新重试' ;
    }
}