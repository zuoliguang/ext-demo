<?php

/**
 * 实现强红包操作
 * 
 * @Author: zuoliguang
 * @Date:   2018-09-30 09:42:10
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-09-30 10:09:48
 */

// 报错机制

// error_reporting(E_ALL ^ E_DEPRECATED);

// ini_set('display_errors', 1);

/**
 * 确保每个人的帐号只能抢一次
 *
 * 多次抢,显示第一次抢的结果
 */

require_once '../redis/Redis_model.php';

$config = [
	
	'host' => '127.0.0.1',

	'port' => 6379,

	'auth' => 'redpackage' // 红包
];

$redis = Redis_model::getInstance($config, ['db_id' => 0]);

$redpackage_key = 'test_redpackage_list'; // 红包key

$redpackage_get_key = 'test_redpackage_get_list'; // 抢红包的记录

$data = $redis->lRange($redpackage_key, 0, -1);

// echo json_encode($data);die();

if (empty($data)) {
	
	echo "红包已经抢完了";

	die();
}

// 这里可以对用户参数做一下其他的限制

$uid = $_GET['uid'] ?: ""; 

if (empty($uid)) {

	echo "打开的方式不对,换个姿势试一试!";

	die();
}

// 判断是否已经抢到了

$result = $redis->hGet($redpackage_get_key, $uid);

if (intval($result)>0) {
	
	echo "已经抢过了: 抢到了 $result";

	die();
}

// 分配红包

$package = $redis->lPop($redpackage_key);

$fee = intval($package);

$redis->hSet($redpackage_get_key, $uid, $fee);

echo "恭喜,恭喜,抢到了 $fee ";

die();