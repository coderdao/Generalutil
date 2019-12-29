<?php
/**
 * PHP基于Redis使用令牌桶算法实现流量控制
 * Description:
 * php基于Redis使用令牌桶算法实现流量控制，使用redis的队列作为令牌桶容器，入队（lPush）出队（rPop)作为令牌的加入与消耗操作。
 *
 * Function:
 * public  add     加入令牌
 * public  get     获取令牌
 * public  reset   重设令牌桶
 *
 * Abo 2019/12/24 22:39
 * Email: abo2013@foxmail.com
 */
class RedisTokenRateLimitUtil
{
    use RedisConnectTrait;

    private $Redis;         // redis对象
    private $tokenQueue;    // 令牌桶
    private $tokenMaxNum;   // 最大令牌数

    /**
     * 初始化
     * @param array $config redis连接设定
     */
    public function __construct( $config, $queue, $max )
    {
        $instances = $this->setServers( $config )->initInstances();


        $this->tokenQueue = $queue;
        $this->tokenMaxNum = $max;
        $this->Redis = $instances[ 0 ];
    }

    /**
     * 加入令牌
     * @param  Int $num 加入的令牌数量
     * @return Int 加入的数量
     */
    public function add($num=0){

        // 当前剩余令牌数
        $curnum = intval( $this->Redis->lSize( $this->tokenQueue ) );

        // 最大令牌数
        $maxnum = intval( $this->tokenMaxNum );

        // 计算最大可加入的令牌数量，不能超过最大令牌数
        $num = ( $maxnum >= ( $curnum + $num ) ? $num : $maxnum - $curnum );

        // 加入令牌
        if($num>0){
            $token = array_fill(0, $num, 1);
            $this->Redis->lPush( $this->tokenQueue, ...$token );
            return $num;
        }

        return 0;
    }

    /**
     * 获取令牌
     * @return Boolean
     */
    public function get(){
        return $this->Redis->rPop( $this->tokenQueue )? true : false;
    }

    /**
     * 重设令牌桶，填满令牌
     */
    public function reset(){
        $this->Redis->delete( $this->tokenQueue );
        $this->add( $this->tokenMaxNum );
    }
}