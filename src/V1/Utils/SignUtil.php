<?php
/**
 * Function:
 * Description:
 * Abo 2019/4/3 20:19
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;


class SignUtil
{
    /**
     * 加密解密字符串
     * @param $string
     * @param $operation  E:加密  D:解密
     * @param string $key 加密的钥匙(密匙);
     * @return bool|mixed|string
     *
     * 使用方法:
     * 加密    :encrypt('str','E','nowamagic');
     * 解密    :encrypt('被加密过的字符串','D','nowamagic');
     */
    function RCEncrypt( $string, $operation, $key='' )
    {
        $key = md5( $key );
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for ( $i = 0; $i <= 255; $i++) {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for ( $j = $i = 0; $i < 256; $i++ ) {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for( $a = $j = $i = 0; $i < $string_length; $i++ ) {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }

        if($operation=='D') {
            if(substr($result,0,8)
                ==substr(md5(substr($result,8).$key),0,8)
            ) {
                return substr($result,8);
            } else {
                return'';
            }
        } else {
            return str_replace('=','',base64_encode($result));
        }
    }
}