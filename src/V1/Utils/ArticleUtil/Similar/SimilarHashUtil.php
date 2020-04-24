<?php
/**
 * Function:
 * Description:
 * Abo 2020/4/24 18:48
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils\ArticleUtil\Similar;


class SimilarHashUtil
{
    protected static $length = 256;
    protected static $search = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
    protected static $replace = array('0000','0001','0010','0011','0100','0101','0110','0111','1000','1001','1010','1011','1100','1101','1110','1111');
    /**
     * [排除的词语]
     *
     * @var array
     */
    private static $_excludeArr = array('的', '了', '和', '呢', '啊', '哦', '恩', '嗯', '吧','你','我','&nbsp;');

    public static function get(array &$set)
    {
        $boxes = array_fill(0, self::$length, 0);
        if (is_int(key($set)))
            $dict = array_count_values($set);
        else
            $dict = &$set;

        foreach ($dict as $element => $weight) {
            if ( in_array($element, self::$_excludeArr )){
                continue;
            }

            $hash = hash('sha256', $element);
            $hash = str_replace(self::$search, self::$replace, $hash);
            $hash = substr($hash, 0, self::$length);
            $hash = str_pad($hash, self::$length, '0', STR_PAD_LEFT);

            for ( $i=0; $i < self::$length; $i++ ) {
                $boxes[$i] += ($hash[$i] == '1') ? $weight : -$weight;
            }
        }
        $s = '';
        foreach ($boxes as $box) {
            if ($box > 0)
                $s .= '1';
            else
                $s .= '0';
        }
        return $s;
    }

    public static function hd($h1, $h2)
    {
        $dist = 0;
        for ($i=0;$i<self::$length;$i++) {
            if ( $h1[$i] != $h2[$i] )
                $dist++;
        }
        return (self::$length - $dist) / self::$length;
    }
}