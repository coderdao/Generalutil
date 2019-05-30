<?php
/**
 * Description: 页面错误,需要报错页支持
 * Abo 2018/12/29 16:07
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Exceptions;

class PageException extends \Exception
{
    public function __construct($code = 500, $message = '') {
        parent::__construct();
        // 索引为数字时，不能用array_merge，否则合并后会重新索引。

        $this->code = empty($code) ? 500 : $code;
        $this->message = $message ? $message : '系统异常,请刷新重试' ;
    }
}