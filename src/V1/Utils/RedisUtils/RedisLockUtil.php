<?php
/**
 * Function:
 * Description:
 * Abo 2019/12/24 22:03
 * Email: abo2013@foxmail.com
 */

class RedisLockUtil
{
    use RedisConnectTrait;

    private $retryDelay;    // 重试间隔
    private $retryCount;    // 重试次数
    private $clockDriftFactor = 0.01;
    private $quorum;

    function __construct(array $servers, $retryDelay = 200, $retryCount = 3)
    {
        $this->setServers( $servers );

        $this->retryDelay = $retryDelay;
        $this->retryCount = $retryCount;
        $this->quorum  = min(count($servers), (count($servers) / 2 + 1));
    }

    public function lock($resource, $ttl)
    {
        $this->initInstances();
        $token = uniqid();
        $retry = $this->retryCount;

        do {
            $n = 0;
            $startTime = microtime( true ) * 1000;
            foreach ($this->instances as $instance) {
                if ($this->lockInstance($instance, $resource, $token, $ttl)) {
                    $n++;
                }
            }

            // 将 2 毫秒添加到漂移中，以考虑 Redis 过期精度，即 1 毫秒，加上小型 TTL 的 1 毫秒最小漂移。
            $drift = ( $ttl * $this->clockDriftFactor ) + 2;
            $validityTime = $ttl - ( microtime( true ) * 1000 - $startTime ) - $drift;
            if ($n >= $this->quorum && $validityTime > 0) {
                return [
                    'validity' => $validityTime,
                    'resource' => $resource,
                    'token'    => $token,
                ];
            } else {
                foreach ( $this->instances as $instance ) {
                    $this->unlockInstance( $instance, $resource, $token );
                }
            }
            // Wait a random delay before to retry
            $delay = mt_rand( floor( $this->retryDelay / 2 ), $this->retryDelay );
            usleep( $delay * 1000 );
            $retry--;
        } while ($retry > 0);
        return false;
    }

    public function unlock(array $lock)
    {
        $this->initInstances();
        $resource = $lock['resource'];
        $token    = $lock['token'];
        foreach ($this->instances as $instance) {
            $this->unlockInstance($instance, $resource, $token);
        }
    }

    private function lockInstance($instance, $resource, $token, $ttl)
    {
        return $instance->set($resource, $token, ['NX', 'PX' => $ttl]);
    }

    private function unlockInstance($instance, $resource, $token)
    {
        // 不但实现了 同一人加锁解锁；而且如果解锁不成功就回滚能够保证 资源的占用
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $instance->eval($script, [$resource, $token], 1);
    }
}
/*
$servers = [
    ['127.0.0.1', 6379, 0.01],
];
$redLock = new RedisLockUtil($servers);
$i2Count = 0;echo '<pre>';

while ( $i2Count < 10 ) {
    $lock = $redLock->lock('test', 10000);
    if ($lock) {
        print_r($lock);
        $ret2Unlock = $redLock->unlock( $lock );

        // !$ret2Unlock 则回滚所有操作
    } else {
        print "Lock not acquired\n";
    }

    $i2Count++;
}
*/