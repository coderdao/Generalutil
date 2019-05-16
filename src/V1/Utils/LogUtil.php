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
    protected static $LEVEL_INFO = 'info';
    protected static $LEVEL_ERROR = 'error';
    protected static $LEVEL_DEBUG = 'debug';
    protected static $LEVEL_EXCEPTION = 'exception';

    protected static $LOG_PATH = '';
    public $defaultPath = '';

    public function __construct( $defaultPath )
    {
        if ( !$defaultPath ) {
            $defaultPath = storage_path( 'logs/logger-'.date( 'Y-m-d' ).'.log' );
        }

        $this->defaultPath = $defaultPath;
    }

    /**
     * 流程记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function info( string $title = '', array $content = [], string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::$LEVEL_INFO );
    }

    /**
     * 调试记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function debug( string $title = '', array $content = [], string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::$LEVEL_DEBUG );
    }

    /**
     * 异常记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function exception( string $title = '', array $content = [], string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::$LEVEL_EXCEPTION );
    }

    /**
     * 错误记录
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @return bool|int
     */
    public static function error( string $title = '', array $content = [], string $logFileName = 'logger' )
    {
        return self::logger( $title, $content, $logFileName, self::$LEVEL_ERROR );
    }

    /**
     * 通用记录方法
     * @param string $title
     * @param array $content
     * @param string $logFileName
     * @param string $logLevel
     * @return bool|int
     */
    protected static function logger( string $title = '', array $content = [], string $logFileName = 'logger', string $logLevel = 'info' )
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
    protected static function getDefaultLogPath( string $logName = 'logger' ){

        if ( is_file( $logName ) && file_exists( $logName ) ) {
            return $logName;
        }

        $logPath = storage_path( 'logs/'.$logName.'-'.date( 'Y-m-d' ).'.log' );
        if( !is_dir( dirname( $logPath ) ) ) {
            logger( '设置日志目录不存在', false );
        }

        return $logPath;
    }

    /**
     * 设置默认日志名 (是否有用 保留态度)
     * @param string $logName
     * @return string
     * @throws \Exception
     */
    protected function setDefaultLogName( string $logName = 'logger' )
    {
        $logPath = storage_path( 'logs/'.$logName.'-'.date( 'Y-m-d' ).'.log' );

        if( is_dir( dirname( $logPath ) ) ) {
            throw new \Exception( '设置日志目录不存在', false );
        }

        return $this->defaultPath = $logPath;
    }

    /**
     * 日子格式
     * @param string $title
     * @param array $content
     * @param string $logLevel
     * @return string
     */
    private static function logFormmat( string $title = '', array $content = [], string $logLevel )
    {
        $logFormmat = '['.date( 'Y-m-d H:i:s' ).'] local.'.$logLevel.': '.$title;
        if ( $content ) {
            $logFormmat .= "\r\n省略内容如下:".json_encode( array_slice( $content, 0, 8) );
        }

        return $logFormmat."\r\n\r\n";
    }
}