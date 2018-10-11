<?php

/**
 * 消费者
 * 
 * @Author: zuoliguang
 * @Date:   2018-10-08 10:55:32
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-11 13:57:43
 */

// 报错机制

error_reporting(E_ALL ^ E_DEPRECATED);

ini_set('display_errors', 1);

$config = [

    'host' => '127.0.0.1',

    'port' => 5672,

    'login' => 'guest',

    'password' => 'guest',

    'vhost' => '/'
];

$e_name = 'test_rabbitmq'; //交换机名

$q_name = 'test_rabbitmq_mq'; //队列名

//创建连接和channel
$conn = new AMQPConnection($config);

if (!$conn->connect()) {

    die("Cannot connect to the broker! \n");

}

$channel = new AMQPChannel($conn);

//创建交换机

$ex = new AMQPExchange($channel);

$ex->setName($e_name);

$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型

$ex->setFlags(AMQP_DURABLE); //持久化

$ex->declare();

//创建队列

$q = new AMQPQueue($channel);

$q->setName($q_name);

$q->setFlags(AMQP_DURABLE); //持久化

$q->declare();     //最好队列object在这里declare()下，否则如果是新的queue会报错

//绑定交换机与队列，并指定路由键，可以多个路由键

$q->bind($e_name, 'key_1');

//阻塞模式接收消息

echo "Message:\n";

$q->consume('processMessage', AMQP_AUTOACK); //自动ACK应答

$conn->disconnect();

/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $queue) {

    var_dump($envelope->getRoutingKey());

    $msg = $envelope->getBody();

    echo " [ x consumer ] : $msg \n";
 
}







