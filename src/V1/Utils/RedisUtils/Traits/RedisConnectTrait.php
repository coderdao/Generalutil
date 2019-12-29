<?php
/**
 * Function:
 * Description:
 * Abo 2019/12/24 22:30
 * Email: abo2013@foxmail.com
 */

trait RedisConnectTrait
{
    private $servers = array();
    private $instances = array();

    /**
     * 设置 Redis 配置
     * @param array $serversConfig
     * @return $this
     * @throws Exception
     */
    private function setServers( array $serversConfig = [ [ '127.0.0.1', 6379, 0.01 ] ] )
    {
        if ( !$serversConfig )
            throw new \Exception( 'Redis链接配置不能为空', false );

        $this->servers = $serversConfig;
        return $this;
    }

    private function initInstances()
    {
        if (empty($this->instances)) {
            foreach ($this->servers as $server) {
                list($host, $port, $timeout) = $server;

                $redis = new \Redis();
                $redis->connect($host, $port, $timeout);
                // $redis->select( ['index'] );

                $this->instances[] = $redis;
            }
        }
    }
}