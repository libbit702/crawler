# crawler

[![Build Status](https://travis-ci.org/libbit702/crawler.svg?branch=master)](https://travis-ci.org/libbit702/crawler)  [![codecov](https://codecov.io/gh/libbit702/crawler/branch/master/graph/badge.svg)](https://codecov.io/gh/libbit702/crawler)  ![](https://img.shields.io/badge/language-php-blue.svg)

### 简介
抓取各信息平台的工具，核心机制源于对网站请求的抓包和网页结构的解析处理，各平台抓取功能可能随着网站改版或接口下线导致不可用，测试方面已添加[travis-ci](https://travis-ci.org) 和[codecov](https://codecov.io), 建议fork后使用travis快速验证有效性

### Prerequisites

* PHP 5.6
* PHPUnit 5.7

## 支持站点

| Site | URL | 视频过滤? | 图片数过滤? | 关键词过滤? | 发布时间过滤? |
| :--: | :-- | :-----: | :-----: | :-----: | :-----: |
| **Weibo账号** | <http://weibo.com/>    |✓|✓|✓|x|
| **Weibo搜索** | <http://weibo.com/>    |✓|✓|✓|x|
| **Weibo话题** | <http://weibo.com/>    |✓|✓|✓|x|
| **Twitter搜索** | <http://twitter.com/>    |✓|✓|✓|✓|
| **Twitter账号** | <http://twitter.com/>    |✓|✓|✓|✓|
| **Instagram搜索** | <https://instagram.com/>    |✓|x|✓|✓|
| **Instagram账号** | <https://instagram.com/>    |✓|x|✓|✓|
| **Bilibili搜索** | <http://bilibili.tv/>    |x|x|x|x|
| **Bilibili账号** | <http://bilibili.tv/>    |x|x|x|x|
| **Bilibili视频集** | <http://bilibili.tv/>    |x|x|x|x|
| **Vlive** | <http://www.vlive.tv/>    |x|x|x|✓|
| **Youtube** | <http://www.youtube.com/>    |x|x|x|✓|
| **豆瓣豆列** | <https://m.douban.com/>    |x|x|x|x|
| **搜狗微信-公众号文章** | <http://weixin.sogou.com/>    |x|x|✓|✓|
| **音悦台搜索** | <http://yinyuetai.com/>    |x|x|✓|✓|

## 使用说明

### 基本用法

引入想要抓取网站对应的类文件，实例化后传入抓取配置参数，执行抓取

```
require_once("class.weibo.search.php");
$crawler = new CrawlerWeiboSearch();
$crawler->setConfig([
 	'keywords' => ['DOTA2'],
 	'debug' => true,
 	'keyword_check' => ['魔兽争霸'],
]);
$crawler->prepareCrawl();
$crawler->executeCrawl();
```

### 测试用例(PHPUnit5.7)

基于PHP5.6.30测试,使用了PHPUnit 5.7 <http://www.phpunit.cn/>

```console
$ phpunit tests/CrawlerWeiboSearchTest

PHPUnit 5.7.21 by Sebastian Bergmann and contributors.

.........                                                           9 / 9 (100%)

Time: 5.53 seconds, Memory: 14.25MB

OK (9 tests, 17 assertions)
```

### 测试用例(PHPUnit4.8)

基于PHP5.4.41测试,使用了PHPUnit 4.8 <http://www.phpunit.cn/>

```console
$ phpunit tests_old/CrawlerInstagramUserTestOld

PPHPUnit 4.8.36 by Sebastian Bergmann and contributors.

.........

Time: 9.6 seconds, Memory: 15.25MB

OK (9 tests, 88 assertions)
```

### Weibo指定账号数据抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|微博话题的id，<http://weibo.com/p/[这部分是话题ID]/super_index>|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测微博中是否带有视频数据|
| image_check |int|设置大于0时，检测微博中图片个数是否满足此处设定|


### Weibo搜索抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索的关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测微博中是否带有视频数据|
| image_check |int|设置大于0时，检测微博中图片个数是否满足此处设定|


### Weibo指定话题数据抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|微博用户的oid，查看网页源代码可见|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测微博中是否带有视频数据|
| image_check |int|设置大于0时，检测微博中图片个数是否满足此处设定|


### Twitter搜索抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索的关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测twitter中是否带有视频数据|
| image_check |int|设置大于0时，检测twitter中图片个数是否满足此处设定|


### Twitter指定账号数据抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|twitter用户的ID，<https://twitter.com/i/profiles/show/[这部分是twitter用户的ID]/timeline/tweets?include_available_features=1&include_entities=1>|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测twitter中是否带有视频数据|
| image_check |int|设置大于0时，检测twitter中图片个数是否满足此处设定|


### Instagram搜索抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索的关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测Instagram中是否带有视频数据|


### Instagram指定账号数据抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|Instagram用户的ID，<https://www.instagram.com/[这部分是ins用户的ID]/>|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测Instagram中是否带有视频数据|


### Bilibili检索抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|


### Bilibili账号抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|B站Up主的ID|
| page |int|抓取结果页数|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|


### Bilibili视频集

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|B站视频集的ID|


### Youtube

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|youtube视频的ID|


### Vlive抓取

```
注意：返回的videos字段是视频播放地址数据对应接口地址，返回JSON数据；‘要获得真实可播放视频地址，需要额外一步网络IO操作
```

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| date |string|参数如20170821,指定vlive数据的日期|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD HH:ii',小于此时间设定的消息会被筛除|


### 豆瓣豆列

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| ids |array|豆列的ID,<https://m.douban.com/doulist/2943106/>|
| page |int|期望抓取页数,wap页单页25条数据|


### 搜狗微信搜索-公众号文章

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|


### 音悦台检索抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|

