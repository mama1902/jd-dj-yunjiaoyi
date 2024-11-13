<?php
namespace app\common\model;

use think\Model;
use think\Env;

class Redis
{

    /**
     * 默认 Redis DB 5
     */
    CONST REDIS_DB = '5';

    /**
     * @var \Redis
     */
    protected $Redis;


    public function __construct(

    )
    {
        $redis_host = Env::get('redis.host', '127.0.0.1');
        $redis_port = Env::get('redis.port', '6379');
        $redis_password = Env::get('redis.password', '');

        $redis = new \Redis();
        $redis->connect($redis_host,$redis_port);
        if ($redis_password) {
            $redis->auth($redis_password);
        }

        $redis->select(self::REDIS_DB);

        $this->Redis = $redis;

    }

    /**
     * 设置队列数据
     * @param $key
     * @return mixed|bool
     */
    public function QueueRedis($key,$datas){
        if(!empty($datas)){
            $this->Redis->Rpush($key, $datas);
            $this->setLock($key.'ing', 1, 86400);
        }
    }

    /**
     * 获取队列数据
     * @param $key
     * @return mixed|bool
     */
    public function getQueueKey($key){
        $length = $this->Redis->lLen($key);
        if($length){
            return $this->Redis->lPop($key);
        }
        return false;
    }

    /**
     * 获取redis缓存
     * @param $key
     * @return bool|mixed|string
     */
    public function get( $key ){
        return $this->Redis->get( $key );
    }

    /**
     * 设置Redis缓存
     * @param $key
     * @param $value
     * @param int $ttl
     */
    public function set($key, $value, $ttl = 60){
        $this->Redis->set($key, $value, $ttl);
    }

    /**
     * 清除redis缓存
     * @param $key
     * @return int
     */
    public function remove( $key ){
        return $this->Redis->del( $key );
    }

    /**
     * 获取redis缓存
     *
     * @return \Redis
     */
    public function getRedis($re = false ){
        if ($re) {

        }
        return $this->Redis;
    }

    /**
     * @function 加锁
     * @param $key 锁名称
     * @param $expTime 过期时间
     */
    public function setLock($key,$value = '1',$expTime= 3800)
    {
        //初步加锁
        $isLock = $this->Redis->set($key, $value, array('nx', 'ex' => $expTime));
        if ($isLock) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $key 解锁
     */
    public function delLock($key)
    {
        $this->Redis->del($key);
    }


    public function consumerByarr($queueName, $callback)
    {
        $timeout = 1;
        while (true){

            if ($item = $this->getQueueKey($queueName) ) {
                $callback($item);
                $this->delLock($queueName.'ing');
            }
            sleep(1);
            $timeout++;
        }
    }

    /**
     * //删除单个实体
    $redis->hDel('hashkey', 'key1');

    //删除整个hash
    $redis->del('hashkey');
    $redis->hSet('hash_name', 'key', 'value');
    $redis->hGet('hash_name', 'key');
    $redis->hGetAll('hash_name');
     *
     */


}
