<?php

/**
 * 借助redis做一个抢红包的用例
 *
 * 这里参照网站上给出的方案来计算红包信息
 * 
 * @Author: zuoliguang
 * @Date:   2018-09-29 08:45:30
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-09-30 09:47:21
 */

// 报错机制

// error_reporting(E_ALL ^ E_DEPRECATED);

// ini_set('display_errors', 1);

/**
 * 业务需求描述 
 *
 * 总金额 250000 元
 *
 * 红包的份额及计算量预先生成
 *
 * 使用 二倍均值法 的红包拆分算法，作为红包拆分方案
 *
 * 拆分算法 : 假设剩余拆分金额为 M, 剩余待拆分红包个数为 N, 红包最小金额为 1 元, 红包最小单位为元, 当前红包的金额为： fee = rand(1, floor(M/N*2))
 *
 * 其中，floor 表示向下取整，rand(min, max) 表示从 [min, max] 区间随机一个值。
 *
 * M/N*2 表示剩余待拆分金额平均金额的 2 倍, 因为 N >= 2，所以 M/N*2 <= M ,表示一定能保证后续红包能拆分到金额
 *
 * 具体是现在代码如下
 * 
 */

$N = 100; // 1000个人

$M = 25000; // 红包总金额

$min = 1; // 限定的最小金额

$m = []; // 拆分的红包列表

/*for ($i = 0; $i < $N-1; $i++) { 
	
	$max = (int) floor( $M / ( $N - $i ) ) * 2;

	$m[$i] = $max ? mt_rand(min($max, $min), max($max, $min)) : 0;

	$M -= $m[$i];
}

$m[] = $M;

echo json_encode($m);*/

/**
 * 为了保证红包金额差异尽量小，先将总金额平均拆分成 N+1 份，将第 N+1 份红包按照上述的红包拆分算法拆分成 N 份，这 N 份红包加上之前的平均金额才作为最终的红包金额。
 */

// 实际的红包拆分算法是

$base_avg = (int) floor( $M / ( $N + 1 ) ); // 红包基数,每个红包会在这个基数上增加

// $base_avg = 200; // 也可以这样指定基数操作

$LM = $M - ( $base_avg * $N ); // 该部分才是要拆分的随机部分

for ($i = 0; $i < $N-1; $i++) { 
	
	$max = (int) floor( $LM / ( $N - $i ) ) * 2;

	$m[$i] = $max ? mt_rand(min($max, $min), max($max, $min)) : 0;

	$LM -= $m[$i];
}

$m[] = $LM;

array_walk($m, function(&$val, $k) use ($base_avg){

	$val += $base_avg;
});

// echo json_encode($m);

// 实际在生成红包的时候还可以具体划分出部分来作为基数平均部分,具体看自己需求及方案定性

// 接下来是redis存储方案

require_once '../redis/Redis_model.php';

$config = [
	
	'host' => '127.0.0.1',

	'port' => 6379,

	'auth' => 'redpackage' // 红包
];

$redis = Redis_model::getInstance($config, ['db_id' => 0]);

// 将红包份额添加到redis保存

$redpackage_key = 'test_redpackage_list'; // 红包key

$len = $redis->lLen($redpackage_key);

if (intval($len) > 0) {
	
	echo "缓存红包已存在";

	die();
}

foreach ($m as $fee) {
	
	$redis->rPush($redpackage_key, $fee);
}

echo '缓存红包加好了';

