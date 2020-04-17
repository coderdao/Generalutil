<?php
/**
 * Function: Curl工具类方法
 * @method makeRequest($method, $url, $params, $content_type, $expire, $is_browser, $the_header, $extend)
 */

namespace Abo\Generalutil\V1\Utils;


class CurlUtil
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    const CONTENT_TYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_URLENCODED_JSON = 'application/json';

    /**
     * 请求方法
     * @params content_type application/x-www-form-urlencoded,application/json
     * @param $method | string $url | array $params
     * @param int $expire | $is_browser | array $the_header | array $extend
     * @return mixed
     */
    public function makeRequest( string $method = CurlUtil::METHOD_GET, string $url, $params = [], int $expire = 5, string $content_type = self::CONTENT_TYPE_URLENCODED, $is_browser=true, $the_header=[], $extend=[]){

        $ch = curl_init($url);


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');


        $_header = [
            'Accept-Language: zh-cn',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
        ];


        if( self::METHOD_GET === $method ){
            if(!empty($params)){
                $url .= (stripos($url, '?') !== false) ? '&' : '?';
                $url .= (is_string($params)) ? $params : http_build_query($params, '', '&');
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }elseif ( self::METHOD_POST === $method ) {
            switch ($content_type){
                case "application/x-www-form-urlencoded":
                    if(true === $is_browser){
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                    }else{
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    }
                    curl_setopt($ch, CURLOPT_POST, true);
                    break;
                case "application/json":
                    if(is_array($params)){
                        $params = json_encode($params,320);
                    }
                    $_header[]='Content-Type: application/json';
                    $_header[]='Content-Length: ' . strlen($params);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    break;
                default:
                    break;
            }
        }else{
            return false;
        }

        if (strpos($url, 'https://') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }



        //if header is empty, use the
        if(!empty($the_header)){

        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);

        if ($expire > 0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $expire);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $expire);
        }

        if (!empty($extend)) {
            curl_setopt_array($ch, $extend);
        }

        $result['result'] = curl_exec($ch);
        $result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['url'] = $url;
        if ($result['result'] === false) {
            $result['result'] = curl_error($ch);
            $result['code'] = -curl_errno($ch);
            $result['url'] = $url;
        }

        curl_close($ch);
        return $result;
    }

    /** 数据后台,盐值产生方法 @param array $data 请求参数 */
    public function getSign( array $params )
    {
        ksort($params);
        $sign_str = '';
        foreach ($params as $key => $value) {
            $sign_str .= $key . '=' . $value . '&';
        }

        $sign_str = substr($sign_str, 0, -1);
        $sign_str .= env( 'E_AIDALAN_SALT' ) ?? '';
        $sign_str = md5($sign_str);
        return $sign_str;
    }
}