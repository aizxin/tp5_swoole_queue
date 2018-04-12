<?php
namespace app\index\controller;


use Enqueue\AmqpExt\AmqpConnectionFactory;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;

class Index
{
    public function index()
    {
        $data = [
            'from'=>'php实战',
            'to'=>'459894336@qq.com',
            'title' => "类型最终将转化为json形式",
            'contents' => "obData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对"
        ];
        $time['delay'] = 60*1000;
        
        $result  = \app\service\Jobs::push('emailSend', '\app\task\EmailSend', 'send', [$data], $time);
        var_dump($result);
        $time['delay'] = 2*60*1000;
        $result  = \app\service\Jobs::push('emailSend', '\app\task\EmailSend', 'send', [$data], $time);
        var_dump($result);
        
    }
    public function test()
    {
        // $config = config('queue');
        // $factory = new AmqpConnectionFactory($config['job']['queue']);
        // $context = $factory->createContext();

        // $context->createTopic($config['job']['queue']['exchange']);

        // $fooQueue = $context->createQueue('MyJob');
        // $fooQueue->addFlag(AmqpQueue::FLAG_DURABLE);
        // $count =$context->declareQueue($fooQueue);

        // var_dump($count);
        // $conn_args = array( 'host'=>'127.0.0.1' , 'port'=> '5672', 'login'=>'whero' , 'password'=> 'whero','vhost' =>'ccwb');
        // $conn = new \AMQPConnection($conn_args);
        // $conn->connect();
        // //设置queue名称，使用exchange，绑定routingkey
        // $channel = new \AMQPChannel($conn);
        // $q = new \AMQPQueue($channel);
        // $q->setName('emailSend');
        // $q->setFlags(\AMQP_DURABLE);
        // $count = $q->declare();
        // dump($count);
        \Swoole\Async::exec("/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb list_queues", function ($result, $status){
            $data = explode("\n",$result);
            for ($i = 2; $i < count($data)-1; $i++){
                preg_match("/[\w\W]+(?=\s)/", $data[$i], $matches);
                $queueNames[$i]['name'] = trim($matches[0]);
                preg_match_all('/\d+/',$data[$i],$r);
                if(isset($r[0][1]) && $r[0][1] == 0){
                    $cmd = '/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb delete_queue '.trim($matches[0]);
                }else{
                    if(isset($r[0][0]) && $r[0][0] == 0){
                        $cmd = '/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb delete_queue '.trim($matches[0]);
                    }
                }
                if($cmd != ''){
                    \Swoole\Async::exec($cmd,function ($result, $status){});
                }
            }
        });
    }
}
