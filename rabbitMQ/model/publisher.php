<?php

/**
 * @Author: zuoliguang
 * @Date:   2018-10-15 11:28:51
 * @Last Modified by:   zuoliguang
 * @Last Modified time: 2018-10-15 11:34:03
 */
require_once './Rabiitmq_model.php';

$test = new Rabiitmq_model();

$datetime = date('Y-m-d H:i:s');

$test->publisher('test_queue_data', 'zlgkey', "[ $datetime ] : this is a test mesages!");