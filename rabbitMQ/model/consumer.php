<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-10-15 11:29:03
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-15 14:17:28
 */
require_once './Rabiitmq_model.php';

$test = new Rabiitmq_model();

$test->consumer('test_queue_data', 'zlgkey', 'processMessage');

/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $queue) {

    $key = $envelope->getRoutingKey();

    // sleep(5); // 每次任务执行的时间长时可依据不同的情况进行时间控制

    $msg = $envelope->getBody();

    // $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答(当队列类型为不自动应答模式时用)

    echo " [ $key ] => $msg \n"; // 每次接受到消息后处理该消息
 
}