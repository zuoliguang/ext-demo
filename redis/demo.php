<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-09-29 08:47:04
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-09-29 15:44:07
 */

// 报错机制

error_reporting(E_ALL ^ E_DEPRECATED);

ini_set('display_errors', 1);

require_once './Redis_model.php';

$config = [
	
	'host' => '127.0.0.1',

	'port' => 6379,

	'auth' => 'zlgcg' // 正式项目中可更新为项目的名称标记
];

/****************************** 初始化redis对象 ************************************/

$redis = Redis_model::getInstance($config, ['db_id' => 0]);

echo "<pre>";

/**************************** string 函数操作及使用场景 *****************************/

// $res1 = $redis->set('name', 'testname');

// var_dump($res1);

// $data1 = $redis->get('name');

// var_dump($data);



// $res2 = $redis->setex('test', 10, 'testname'); // 10秒过期

// var_dump($res2);

// $data2 = $redis->get('test');

// var_dump($data2);



/**************************** hash 函数操作及使用场景 *****************************/

// $id_key = 1223;

// $info = [
	
// 	'a' => 'aaaaaaaaa',

// 	'b' => 'bbbbbbbbb',

// 	'c' => 1223,

// 	'd' => 'this is a test info!'
	
// ];



// foreach ($info as $key => $val) {
	
// 	$redis->hSet($id_key, $key, $val); // 设置字段
	
// }

// $keys = array_keys($info);

// foreach ($keys as $field) {
	
// 	$data = $redis->hGet($id_key, $field); // 获取字段

// 	var_dump($data);
	
// }

// $data = $redis->hLen($id_key); // 元素数量

// var_dump($data);



// $redis->hIncrBy($id_key, 'c', 100); // c字段+100

// $data = $redis->hGet($id_key, 'c');

// var_dump($data);

// $redis->hIncrBy($id_key, 'c', -100); // c字段-100

// $data = $redis->hGet($id_key, 'c');

// var_dump($data);



// $data = $redis->hGetAll($id_key); // key下的所有[k_v]数据

// var_dump($data);



/**************************** 有序 函数操作及使用场景 *****************************/

// $key = 'test_zkeys';

// $val = 'score';


/*for ($i=0; $i < 100; $i++) { // 模拟随机给制定用户累加分值
	
	$name = mt_rand(1, 10)."_".$val;

	$score = mt_rand(0, 100);

	$redis->zIncrby($key, $score, $name);
}*/



/*$data = $redis->zRangeByScore($key); // 获取排序

$zlist = [];

foreach ($data as $value) {

	$zlist[] = [

		'name' => $value,

		'score' => $redis->zScore($key, $value)
	];
}*/



/*usort($zlist, function($a, $b){ // 分值有高到底排序

	return $b['score'] - $a['score'];
});

print_r($zlist);*/



/**************************** list 函数操作及使用场景 *****************************/

// $key = 'list_data';

// $value = 'data_';

// for ($i = 1; $i <= 10; $i++) { 
	
// 	$redis->lPush($key, $value.$i); // 头部添加元素
	
// 	$redis->rPush($key, $value.$i); // 尾部添加元素
	
// }

// $res = $redis->lRange($key, 0, -1);

// print_r($res);






