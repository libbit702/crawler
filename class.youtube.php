<?php

require_once('class.base.php');

class CrawlerYoutube extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {

		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("ids required for youtube");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("ids cannot be empty for youtube");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['ids'] as $ei) {
			$crawl_url = 'https://www.youtube.com/watch?v='.$ei;

			$this->log("开始请求地址:$crawl_url");

			$this->snoopy->fetch($crawl_url);

			if ($this->snoopy->results === false) {
				continue;
			}

			$crawl_result = $this->snoopy->results;
			
			preg_match('#ytInitialData"\]([\s\S]*?)window\["ytInitialPlayerResponse#', $crawl_result, $matches);
			$initialData = trim(str_replace(' = ', '', $matches[1]));
			$initialData = substr($initialData, 0, -1);
			$initialData = json_decode($initialData,true);
			$result = $initialData['contents']['twoColumnWatchNextResults']['results']['results']['contents'];

			$obj = [];
			$obj['title'] = $result['0']['videoPrimaryInfoRenderer']['title']['simpleText'];
			$obj['watch_time'] = str_replace('Published on ', '', $result['1']['videoSecondaryInfoRenderer']['dateText']['simpleText']);

			$obj['youtube_view'] = str_replace(array(',',' views'), '', $result['0']['videoPrimaryInfoRenderer']['viewCount']['videoViewCountRenderer']['viewCount']['simpleText']);
			$obj['created_at_time'] = date('Y-m-d H:i', strtotime($obj['watch_time']));
			$obj['url_author'] = $result['1']['videoSecondaryInfoRenderer']['owner']['videoOwnerRenderer']['title']['runs']['0']['text'];

			$obj['link'] = $crawl_url;
			$obj['pics'] = [];

			if (empty($obj['title']) || empty($obj['youtube_view'])) {
				continue;
			}

			$this->crawl_messages[] = $obj;
		}
	}

	public function doKeywordCheck() {
		//Youtube数据不检测关键字
	}

	public function doPublicTimeCheck() {
		//Youtube数据不检测发布时间
	}

	public function doImageCheck() {
		//Youtube数据不检测图片
	}

	public function doVideoCheck() {
		//Youtube数据不检测视频
	}

	/**
	 * 经过过滤后的数据，可以做后续处理
	 */
	public function doMessage() {
		// print_r($this->crawl_messages);
	}
}