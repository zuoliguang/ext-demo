<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-09-27 10:33:28
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-09-27 15:43:43
 */

// 结合当前环境配置zookeeper和kafka的快捷键以php的调用方式
// 启动zookeeper 时需要指定 zookeeper 的配置文件
// alias zkstart='/home/softwore/kafka_2.10-0.9.0.0/bin/zookeeper-server-start.sh /home/softwore/kafka_2.10-0.9.0.0/config/zookeeper.properties'
// alias zkstop='/home/softwore/kafka_2.10-0.9.0.0/bin/zookeeper-server-stop.sh'
// 启动 server 时 指定 server 的配置文件
// alias kfstart='/home/softwore/kafka_2.10-0.9.0.0/bin/kafka-server-start.sh /home/softwore/kafka_2.10-0.9.0.0/config/server.properties'
// alias kfstop='/home/softwore/kafka_2.10-0.9.0.0/bin/kafka-server-stop.sh'
// 消费者
// alias kfcs='/home/softwore/kafka_2.10-0.9.0.0/bin/kafka-console-consumer.sh'
// 生产者
// alias kfpr='/home/softwore/kafka_2.10-0.9.0.0/bin/kafka-console-producer.sh'

class Kafka
{
	private $_zookeeperPort = '2181';

    private $_serverPort = '9092';

    private $_brokerList = 'localhost:9092';// Kafka节点，一个Kafka节点就是一个broker；可以写多个 使用 , 分割

    // private $_brokerList = 'localhost:9092,localhost:9093,localhost:9094';// Kafka节点，一个Kafka节点就是一个broker；可以写多个 使用 , 分割

    private $_topic = '';// 主题

    private $_partition = 0;// 主题在 物理上的分组

    private $_logFile = '/home/wwwlogs/kafka.log';

    private $_consume = null;// 消费者

    private $_producer = null;// 生产者

	function __construct()
	{
		if (empty($this->_brokerList)) throw new \Exception('broker not found');

        $kafka = new \RdKafka\Producer();

        if (empty($kafka)) throw new \Exception('RdKafka not found');

        $kafka->setLogLevel(LOG_DEBUG);

        if (!$kafka->addBrokers($this->_brokerList)) throw new \Exception('producer error');

        $this->_producer = $kafka;
	}

    /**
     * 发送队列消息
     * @author zuoliguang 2018-09-27
     * @param  [type] $topic   [description]
     * @param  array  $message [description]
     * @return [type]          [description]
     */
    public function send($topic, $message = [])
    {
        // 创建一个 topic

        $newTopic = $this->_producer->newTopic($topic);

        return $newTopic->produce(RD_KAFKA_PARTITION_UA, $this->_partition, json_encode($message));
    }

    /**
     * 拉取历史数据
     * @author zuoliguang 2018-09-27
     * @param  [type]  $topic    [description]
     * @param  integer $group_id [description]
     * @return [type]            [description]
     */
    public function pullMessage($topic, $group_id=1)
    {
        $conf = new \RdKafka\Conf();

        // 配置groud.id 具有相同 group.id 的consumer 将会处理不同分区的消息，所以同一个组内的消费者数量如果订阅了一个topic， 那么消费者进程的数量多于 多于这个topic 分区的数量是没有意义的。
        
        $conf->set('group.id', $group_id);// 定义消费组id

        $conf->set('metadata.broker.list', $this->_brokerList);

        // 定义 topic

        $topicConf = new \Rdkafka\TopicConf();

        $topicConf->set('auto.offset.reset', 'smallest');// 从开头获取消息

        $conf->SetDefaultTopicConf($topicConf);

        $consumer = new \Rdkafka\KafkaConsumer($conf);

        $consumer->subscribe([$topic]);// 订阅主题,可以订阅多个

        // echo 'waiting for producer message'.PHP_EOL;

        $data = [];

        // 处理消息

        while (true) {

            $message = $consumer->consume(120 * 1000);

            if ($message->err == 0) {
            	
            	$data[] = $message->payload;

            	// var_dump($data);

            } else {

            	break;
            }

            // sleep(1);// 一定要加，要不然 CPU的消耗 会越来越大
        }

        return $data;
    }
}

