#!/usr/bin/env php
<?php
define('APP_PATH', __DIR__ . '/application/');
date_default_timezone_set('Asia/Shanghai');
$config  = require_once APP_PATH . 'config.php';
// $cmd = '/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb list_queues';
// exec($cmd, $res);
// var_dump($res);
// for ($i = 2; $i < count($res); $i++){
//     // delayed
//     preg_match("/[\w\W]+(?=\s)/", $res[$i], $matches);
//     $queueNames[$i]['name'] = trim($matches[0]);
//     preg_match_all('/\d+/',$res[$i],$r);
//     if(isset($r[0][1]) && $r[0][1] == 0){
//         $cmd = '/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb delete_queue '.trim($matches[0]);
//         exec($cmd, $re1);
//         $queueNames[$i]['count'] = $r[0][1];
//     }else{
//         if(isset($r[0][0]) && $r[0][0] == 0){
//             $cmd = '/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb delete_queue '.trim($matches[0]);
//             exec($cmd, $re1);
//         }
//         $queueNames[$i]['count'] = $r[0][0];
//     }
// }
// var_dump($queueNames);


// $process = new \Swoole\Process(function (\Swoole\Process $childProcess) {
//     // 不支持这种写法
//     $data = $childProcess->exec('/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb list_queues');
//     var_dump($data);
//      // 封装 exec 系统调用
//      // 绝对路径
//      // 参数必须分开放到数组中
// //     $childProcess->exec('/usr/local/bin/php', ['/var/www/project/yii-best-practice/cli/yii', 
// //     't/index', '-m=123', 'abc', 'xyz']); // exec 系统调用
// });
// $process->start(); // 启动子进程


\Swoole\Async::exec("/usr/local/Cellar/rabbitmq/3.7.4/sbin/rabbitmqctl -p ccwb list_queues", function ($result, $status){
    $data = explode("\n",$result);
    for ($i = 2; $i < count($data)-1; $i++){
        preg_match("/[\w\W]+(?=\s)/", $data[$i], $matches);
        $queueNames[$i]['name'] = trim($matches[0]);
        preg_match_all('/\d+/',$data[$i],$r);
        $cmd = '';
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
// var_dump($pid);
