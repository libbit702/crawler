<?php

require_once('class.base.php');

class CrawlerYinyuetaiSearch extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['keywords'])) {
			throw new InvalidArgumentException("keywords required for yinyuetai search");
		}

		if (empty($this->crawl_config['keywords'])) {
			throw new InvalidArgumentException("keywords cannot be empty for yinyuetai search");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for yinyuetai search");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['keywords'] as $kw) {
			for ($i=1; $i <= $page; $i++) { 
				$crawl_url = 'http://so.yinyuetai.com/search/video-search?callback=&_api=get.videoList&_mock=false&keyword='.rawurlencode($kw).'&pageIndex='.$i.'&pageSize=10&offset=0';

				$this->log("开始请求地址:$crawl_url");

				$this->snoopy->fetch($crawl_url);

				if ($this->snoopy->results === null) {
					continue;
				}

				$weibo_result = $this->snoopy->results;

				$this->log("请求返回结果:$weibo_result");

				$result = json_decode($weibo_result, true);

				if (!is_array($result) || !isset($result['videos']['data'])) {
					continue;
				}

				$nodes = $result['videos']['data'];

				foreach ($nodes as $node) {
					$node['created_at_time'] = str_replace('.', '-', $node['pubDate']);
					$node['pics'] = [];
					$node['pics'][] = $node['headImg'];
					$this->crawl_messages[] = $node;
				}
			}
		}
	}

	public function doKeywordCheck() {
		if (isset($this->crawl_config['keyword_check'])) {
			$keyword_filter = $this->crawl_config['keyword_check'];
			if (!empty($keyword_filter)) {
				foreach ($this->crawl_messages as $ssk => $ssc) {
					foreach ($keyword_filter as $kf) {
						if (mb_strpos($ssc['title'], $kf) !== false) {
							$this->log('微博正文筛出关键字匹配成功，删除数据:' . print_r($ssc, true));
							unset($this->crawl_messages[$ssk]);
						}
					}
				}
			}
		}
	}

	public function doPublicTimeCheck() {
		if (isset($this->crawl_config['public_time_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if ($ssc['created_at_time'] < $this->crawl_config['public_time_check']) {
					$this->log('不满足发布时间限制，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	/*
	public function doImageCheck() {
		//yinyuetai数据不检测图片
	}

	public function doVideoCheck() {
		//yinyuetai数据不检测视频
	}
	*/

	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}