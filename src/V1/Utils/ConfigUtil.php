<?php

namespace Abo\Generalutil\V1\Utils;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class ConfigUtil
{
    public $configPath;
    public $items = [];

    public function __construct( string $configPath )
    {
        $this->configPath = $configPath;
        $this->items = $this->getConfigFromFile( $configPath );
    }

    public function get( $key, $default = null )
    {
        $temItems = $this->items;
        if ( strstr( $key, '.' ) ) {
            $keys = explode( '.', $key );

            foreach ( $keys as $v2Key ) {
                $temItems = isset( $temItems[ $v2Key ] ) ? $temItems[ $v2Key ] : $default ;
            }
            $ret2Return = $temItems;
        } else {
            $ret2Return = isset( $temItems[ $key ] ) ? $temItems[ $key ] : $default ;
        }

        return $ret2Return;
    }

    /**
     * 获取配置文件
     * @param $configName
     * @return array|mixed
     */
    protected function getConfigFromFile( $configName )
    {
        $configPath = __DIR__.'/../Config/'.$configName;
        if ( file_exists( $configName ) ) {
            $configPath = $configName;
        }

        if ( !file_exists( $configPath ) ) {
            return [];
        }

        return include ( $configPath );
    }

}
