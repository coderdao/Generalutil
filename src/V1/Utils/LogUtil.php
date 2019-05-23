<?php
/**
 * Function:
 * Description:
 * Abo 2019/4/10 15:05
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;

class LogUtil
{
    /** 日志等级 @var string  */
    const LEVEL_INFO = 'info';
    const LEVEL_ERROR = 'error';
    const LEVEL_DEBUG = 'debug';
    const LEVEL_EXCEPTION = 'exception';

    /**
     * 流程记录
     * @param string $title
     * @param $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function info( string $title, $content, string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::LEVEL_INFO );
    }

    /**
     * 调试记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function debug( string $title, $content, string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::LEVEL_DEBUG );
    }

    /**
     * 异常记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function exception( string $title, $content, string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::LEVEL_EXCEPTION );
    }

    /**
     * 错误记录
     * @param string $title
     * @param $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function error( string $title, $content, string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::LEVEL_ERROR );
    }

    /**
     * 通用记录方法
     * @param string $title
     * @param $content
     * @param string $logFileName
     * @param string $logLevel
     * @return bool|int
     */
    protected static function logger( string $title, $content, string $logFileName = 'logger', string $logLevel = 'info' )
    {
        $logPath = self::getDefaultLogPath( $logFileName );
        $text2Log = self::logFormmat( $title, $content, $logLevel );

        return file_put_contents( $logPath, $text2Log, FILE_APPEND );
    }

    /**
     * 获取默认 日志路径
     * @param string $logName
     * @return string
     * @throws \Exception
     */
    protected static function getDefaultLogPath( string $logName = 'logger' )
    {
        if ( is_file( $logName ) && file_exists( $logName ) ) {
            return $logName;
        }

        $logPath = self::logPath().'/logs/'.basename( $logName ).'-'.date( 'Y-m-d' ).'.log';
        if( !is_dir( dirname( $logPath ) ) ) {
            throw new \Exception( '设置日志目录不存在:'.$logPath, false );
        }

        return $logPath;
    }

    /**
     * 日子格式
     * @param string $title
     * @param $content
     * @param string $logLevel
     * @return string
     */
    private static function logFormmat( string $title, $content, string $logLevel )
    {
        $logFormmat = '['.date( 'Y-m-d H:i:s' ).'] local.'.$logLevel.': '.$title;
        if ( $content ) {
            if ( is_array( $content ) ) {
                $logFormmat .= "\r\n省略内容如下:".json_encode( array_slice( $content, 0, 8) );
            }elseif ( is_string( $content ) ) {
                $logFormmat .= "\r\n省略内容如下:".$content;
            }else{
                $logFormmat .= "\r\n省略内容如下:".json_encode( $content );
            }
        }

        return $logFormmat."\r\n\r\n";
    }

    /** 项目根目录 @return string */
    private static function logPath():string
    {
        $rootPath = $logPath = self::appRootPath();
        $storagePath = $rootPath.'/storage';
        $runtimePath = $rootPath.'/runtime';

        if ( is_dir( $storagePath ) ) {
            $logPath = $storagePath;
        }elseif ( is_dir( $runtimePath ) ) {
            $logPath = $runtimePath;
        }

        return $logPath;
    }

    /** 项目根目录 @return string */
    private static function appRootPath():string
    {
        return dirname( __FILE__, 4 );
    }
}