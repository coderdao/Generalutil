<?php
/**
 * Function:
 * Description:
 * Abo 2019/12/26 22:41
 * Email: abo2013@foxmail.com
 */

class RedisDelayQueueUtil
{
    use RedisConnectTrait;

    const QUEUE_PREFIX = 'delay_queue:';
    protected $redis = null;
    protected $key = '';

    public function __construct( string $queueName, array $config = [] )
    {
        $instances = $this->setServers( $config )->initInstances();

        $this->key = self::QUEUE_PREFIX . $queueName;
        $this->redis = $instances[ 0 ];
        // $this->redis->auth($config['auth']);
    }

    public function delTask($value)
    {
        return $this->redis->zRem($this->key, $value);
    }

    public function getTask()
    {
        //获取任务，以0和当前时间为区间，返回一条记录
        return $this->redis->zRangeByScore( $this->key, 0, time(), [ 'limit' => [ 0, 1 ] ] );
    }

    public function addTask($name, $time, $data)
    {
        //添加任务，以时间作为score，对任务队列按时间从小到大排序
        return $this->redis->zAdd(
            $this->key,
            $time,
            json_encode([
                'task_name' => $name,
                'task_time' => $time,
                'task_params' => $data,
            ], JSON_UNESCAPED_UNICODE )
        );
    }

    public function run()
    {
        //每次只取一条任务
        $task = $this->getTask();
        if (empty($task)) {
            return false;
        }

        $task = $task[0];
        //有并发的可能，这里通过zrem返回值判断谁抢到该任务
        if ($this->delTask($task)) {
            $task = json_decode($task, true);

            //处理任务
            echo '任务：' . $task['task_name'] . ' 运行时间：' . date('Y-m-d H:i:s') . PHP_EOL;

            return true;
        }

        return false;
    }
}

$dq = new RedisDelayQueueUtil('close_order', [
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => '',
    'timeout' => 60,
]);

$dq->addTask('close_order_111', time() + 30, ['order_id' => '111']);
$dq->addTask('close_order_222', time() + 60, ['order_id' => '222']);
$dq->addTask('close_order_333', time() + 90, ['order_id' => '333']);

set_time_limit(0);

$i2Count = 0;
while ( 10 < $i2Count ) {
    $dq->run();
    usleep(100000);
    $i2Count++;
}
