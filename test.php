<?php
error_reporting(0);

require_once("class.weibo.search.php");
require_once("class.weibo.user.php");
require_once("class.weibo.topic.php");

// $crawler = new CrawlerWeiboSearch();
// $crawler->setConfig([
// 	'keywords' => ['DOTA2'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// ]);

// $crawler = new CrawlerWeiboUser();
// $crawler->setConfig([
// 	'ids' => ['1731986465'],
// 	'debug' => true,
// 	'keyword_check' => ['老师'],
// 	'public_time_check' => '2017-07-20 16:00',
// 	'image_check' => 8,
// ]);

$crawler = new CrawlerWeiboTopic();
$crawler->setConfig([
	'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
	'debug' => true,
	'keyword_check' => ['魔兽争霸'],
]);

$crawler->prepareCrawl();
$crawler->executeCrawl();