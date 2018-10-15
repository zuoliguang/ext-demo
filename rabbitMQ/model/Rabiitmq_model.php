<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-10-11 13:29:25
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-15 14:16:01
 */

class Rabiitmq_model
{
	/**
	 * 交换机类型
	 * 
	 * 1、AMQP_EX_TYPE_DIRECT
	 * 直连交换器 : 该交换机将会对绑定键和路由键进行精确匹配, 只有精确匹配该路由键的队列，才会发送消息到该队列
	 * 
	 * 2、AMQP_EX_TYPE_FANOUT
	 * 扇形交换器 : 会发送消息到它所知道的所有队列，每个消费者获取的消息都是一致的
	 * 
	 * 3、AMQP_EX_TYPE_TOPIC
	 * 话题交换器 : 该交换机会对路由键正则匹配，必须是*(一个单词)、#(多个单词，以.分割) 、user.key.abc.* 类型的key
	 * 
	 * 4、AMQP_EX_TYPE_HEADER
	 * 头部交换器
	 * 
	 * @var [type]
	 */
	public $extype = AMQP_EX_TYPE_DIRECT; //默认 direct类型 


	/**
	 * 交换机保持方式
	 * 
	 * 1、AMQP_PASSIVE 
	 * 被动模式的交换机和队列不能被重新定义,但是如果交换机和队列不存在,代理将扔出一个错误提示
	 * 
	 * 2、AMQP_DURABLE 
	 * 持久化交换机和队列,当代理重启动后依然存在,并包括它们中的完整数据
	 * 
	 * 3、AMQP_AUTODELETE
	 * 对交换机而言,自动删除标志表示交换机将在没有队列绑定的情况下, 即客户端断开时将被自动删除,
	 * 如果从没有队列和其绑定过,这个交换机将不会被删除.
	 * 
	 * @var [type]
	 */
	public $save_type = AMQP_DURABLE; // 默认持久化


	/**
	 * 消息的接受标记方式
	 * 0、null 或者不添加类型
	 * 队列接受消息后对消息不做接受消息处理，每次队列重启都会将原来的消息接收一次
	 * 
	 * 1、AMQP_AUTOACK
	 * 当在队列get方法中作为标志传递这个参数的时候,消息将在被服务器输出之前标志为[已收到]标记，以后消息重启就不会接受以前的消息
	 * 
	 * @var [type]
	 */
	public $ack_type = AMQP_AUTOACK; // 自动ACK应答


	/**
	 * 队列的配置信息
	 * @var [type]
	 */
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
		$this->conn = new AMQPConnection($this->config); //创建连接和channel

		if (!$this->conn->connect()) {

		    die("Cannot connect to the broker!\n");
		}

		$this->channel = new AMQPChannel($this->conn); //创建频道

		$this->exchange = new AMQPExchange($this->channel); //创建交换机

		$this->queue = new AMQPQueue($this->channel); //创建队列
	}

	/**
	 * 发布消息
	 * 
	 * @author zuoliguang 2018-10-11
	 * @param  [type] $exchange_name 交换机名称
	 * @param  [type] $key           key
	 * @param  [type] $value         value
	 * @return [type]                [description]
	 */
	public function publisher($exchange_name, $key, $value)
	{
		$this->exchange->setName($exchange_name);

		$this->exchange->setType($this->extype); //direct类型

		$this->exchange->setFlags($this->save_type); //持久化

		$this->exchange->declare();

		$this->exchange->publish($value, $key);
	}

	/**
	 * (阻塞式接收消息)接受消息
	 * 
	 * @author zuoliguang 2018-10-11
	 * @param  [type] $exchange_name 交换机名称
	 * @param  [type] $key           key
	 * @param  [type] $type          模式 是否持久化,默认持久化
	 * @return [type]                [description]
	 */
	public function consumer($exchange_name, $key, $callback_function_name)
	{
		$queue_name = $exchange_name.'_queue';

		// ---------------------------------------------------
		
		$this->exchange->setName($exchange_name);

		$this->exchange->setType($this->extype); //direct类型

		$this->exchange->setFlags($this->save_type); //持久化

		$this->exchange->declare();

		// ----------------------------------------------------

		$this->queue->setName($queue_name);

		$this->queue->setFlags($this->save_type); //持久化

		$this->queue->declare();     //最好队列object在这里declare()下，否则如果是新的queue会报错

		//绑定交换机与队列，并指定路由键，可以多个路由键

		$this->queue->bind($exchange_name, $key);

		if (!function_exists($callback_function_name)) {
			
			die("Cannot find the callback_function!\n");
		}

		$this->queue->consume($callback_function_name, $this->ack_type); 

		$this->conn->disconnect();
	}
}