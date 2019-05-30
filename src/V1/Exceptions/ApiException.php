<?php
/**
 * Description: 接口错误,返回json
 * Abo 2018/12/29 16:07
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Exceptions;

class ApiException extends BaseException
{
    protected function exceptions() {
        return array(
            901 => 'validator error',
            100100 => '两次密码输入不一致',
            100101 => '手机验证码不正确',
            100102 => '身份证号码不正确',
            100103 => '修改密码失败',
            100104 => '发送验证码失败',
            100105 => '登录态失效，请重新登录',
            100106 => '操作失败',
            100107 => '非法用户',
            100108 => '图片格式不对',
        );
    }
}