<?php
error_reporting(0);

require_once("class.weibo.search.php");
require_once("class.weibo.user.php");

// $crawler = new CrawlerWeiboSearch();
// $crawler->setConfig([
// 	'keywords' => ['DOTA2'],
// 	'debug' => true,
// 	'keyword_filter' => ['魔兽争霸'],
// ]);

$crawler = new CrawlerWeiboUser();
$crawler->setConfig([
	'ids' => ['1731986465'],
	'debug' => true,
	'keyword_filter' => ['老师'],
	'public_time_limit' => '2017-07-20 16:00',
	'image_limit' => 8,
]);

$crawler->prepareCrawl();
$crawler->executeCrawl();