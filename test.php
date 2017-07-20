<?php
error_reporting(0);
require_once("class.weibo.search.php");
$crawler = new CrawlerWeiboSearch();
$crawler->setConfig([
	'keywords' => ['DOTA2'],
	'debug' => true,
	'keyword_filter' => ['魔兽争霸'],
]);
$crawler->prepareCrawl();
$crawler->executeCrawl();