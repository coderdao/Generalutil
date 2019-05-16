<?php
/**
 * Function: 字符串工具类方法
 * @method randonString( int $length = 8 )  随机字母字符串
 * @method randonNum( int $length = 6 )     随机数组字符串
 */

namespace Abo\Generalutil\V1\Utils;


class StringUtil
{

    // 随机字母字符串
    public function randonString( $length = 8 )
    {
        return substr( md5( time() ), 0, $length );
    }

    // 随机 数子字符串
    public function randonNum( $length = 6 )
    {
        $num2Return = '';
        for ($i = 0; $i<$length; $i++)
        {
            $num2Return .= mt_rand(0,9);
        }

        return $num2Return;
    }

    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     */
    public function camelize($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }

    /**
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     */
    public function uncamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}