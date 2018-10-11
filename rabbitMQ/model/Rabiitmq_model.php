<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-10-11 13:29:25
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-11 14:03:27
 */

class Rabiitmq_model
{

	private $config = [

	    'host' => '127.0.0.1',

	    'port' => 5672,

	    'login' => 'guest',

	    'password' => 'guest',

	    'vhost' => '/'
	];

	protected $conn; // 连接资源

	protected $channel; // 频道

	protected $exchange; // 交换机

	protected $queue; // 队列

	function __construct()
	{
		
	}

	/**
	 * 发布消息
	 * @author zuoliguang 2018-10-11
	 * @param  [type] $exchange_name 交换机名称
	 * @param  [type] $key           key
	 * @param  [type] $value         value
	 * @return [type]                [description]
	 */
	public function publisher($exchange_name, $key, $value)
	{
		# code...
	}

	/**
	 * 接受消息
	 * @author zuoliguang 2018-10-11
	 * @param  [type] $exchange_name 交换机名称
	 * @param  [type] $key           key
	 * @param  [type] $type          模式 是否持久化,默认持久化
	 * @return [type]                [description]
	 */
	public function consumer($exchange_name, $key, $type)
	{
		# code...
	}
}