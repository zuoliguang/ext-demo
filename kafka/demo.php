<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-09-27 14:23:04
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-08 17:20:25
 */

require_once './Kafka.php';

$kafka = new Kafka();

/*------- 存储/获取测试 ----------------------------------------------------*/

$topic = "my_test_topic";

// ------------------设置信息

$start = microtime();

for ($i=1; $i <= 100; $i++) { 

	$kafka->send($topic, ['data' => $i . ': hello kafka!']);

}

$end = microtime();

$time = $end - $start;

$avg = $time / 10;

echo "set over! <br/>";

echo " totalTime : $time ms ; avg: $avg ms ;";

// // 测试结果 100 条信息保存 总时间 0.00065ms 平均时间 0.000065ms

// ------------------获取数据

// $data = $kafka->pullMessage($topic);

// // 当有不同的消费者使用该数据时，使用不同的 group_id 来区分获取，这样保证每个消费者都会获取相同的数据

// foreach ($data as &$json) {

// 	$json = json_decode($json, true);

// }

// echo json_encode($data);

/*------- 应用场景分析 ----------------------------------------------------*/

// 1、消息队列，适用于并发量大，处理优先级较低的数据处理.

// 2、例如 客户端行为记录、消息订阅、日志延迟处理等.

// 3、处理速度快.




































