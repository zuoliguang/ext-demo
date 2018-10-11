<?php

/**
 * 生产者
 * 
 * @Author: zuoliguang
 * @Date:   2018-10-08 10:56:00
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-11 13:02:12
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

//创建连接和channel

$conn = new AMQPConnection($config);

if (!$conn->connect()) {

    die("Cannot connect to the broker!\n");
}

$channel = new AMQPChannel($conn);

//创建交换机

$e_name = 'test_rabbitmq'; //交换机名

$ex = new AMQPExchange($channel);

$ex->setName($e_name);

$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型

$ex->setFlags(AMQP_DURABLE); //持久化

$ex->declare();

$sStr = date('Y-m-d H:i:s') . ' : this is an rabbitmq test MQ!';

$ex->publish($sStr, 'key_1');

echo "send over! \n";
