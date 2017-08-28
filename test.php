<?php
error_reporting(E_ALL);

require_once("class.weibo.search.php");
require_once("class.weibo.user.php");
require_once("class.weibo.topic.php");
require_once("class.twitter.search.php");
require_once("class.twitter.user.php");
require_once("class.instagram.search.php");
require_once("class.instagram.user.php");
require_once("class.bilibili.search.php");
require_once("class.bilibili.user.php");


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

// $crawler = new CrawlerWeiboTopic();
// $crawler->setConfig([
// 	'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// ]);

// $crawler = new CrawlerTwitterSearch();
// $crawler->setConfig([
// 	'keywords' => ['exo'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// ]);

// $crawler = new CrawlerTwitterUser();
// $crawler->setConfig([
// 	'ids' => ['KARD_Official'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// ]);

// $crawler = new CrawlerInstagramSearch();
// $crawler->setConfig([
// 	'keywords' => ['exo'],
// 	'debug' => true,
// 	'page' => 3,
// 	'keyword_check' => ['魔兽争霸'],
// ]);

// $crawler = new CrawlerInstagramUser();
// $crawler->setConfig([
// 	'ids' => ['__youngbae__'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// 	'page' => 2,
// ]);

// $crawler = new CrawlerBilibiliSearch();
// $crawler->setConfig([
// 	'keywords' => ['exo'],
// 	'debug' => true,
// 	'keyword_check' => ['魔兽争霸'],
// 	'page' => 1,
// ]);

$crawler = new CrawlerBilibiliUser();
$crawler->setConfig([
	'ids' => ['4537274'],
	'debug' => true,
	'page' => 3,
]);

$crawler->prepareCrawl();
$crawler->executeCrawl();