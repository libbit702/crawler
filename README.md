# crawler

## 支持站点

| Site | URL | 视频过滤? | 图片数过滤? | 关键词过滤? | 发布时间过滤? |
| :--: | :-- | :-----: | :-----: | :-----: | :-----: |
| **Weibo账号** | <http://weibo.com/>    |✓|✓|✓|x|
| **Weibo搜索** | <http://weibo.com/>    |✓|✓|✓|x|
| **Twitter搜索** | <http://twitter.com/>    |✓|✓|✓|✓|
| **Twitter账号** | <http://twitter.com/>    |✓|✓|✓|✓|
| **Instagram搜索** | <https://instagram.com/>    |✓|✓|✓|✓|
| **Instagram账号** | <https://instagram.com/>    |✓|✓|✓|✓|
| **Bilibili搜索** | <http://bilibili.tv/>    |x|x|✓|✓|
| **Bilibili账号** | <http://bilibili.tv/>    |x|x|x|✓|

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
### Weibo账号抓取

| 参数名称 | 数据类型 | 说明 |
| :--: | :-- | :-- |
| keywords |array|检索的关键字|
| page |int|抓取结果页数|
| keyword_check |array|筛除关键字，抓取结果中不得出现此参数指定的文字|
| public_time_check |string|消息发布时间,格式为'YYYY-MM-DD',小于此时间设定的消息会被筛除|
| video_check |int|设置大于0时，检测微博中是否带有视频数据|
| image_check |int|设置大于0时，检测微博中图片个数是否满足此处设定|

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
