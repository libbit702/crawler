<?php

require_once('class.base.php');

class CrawlerWeiboUser extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids required for weibo user");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids cannot be empty for weibo user");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for weibo user");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['ids'] as $ei) {
			if (!is_numeric($ei)) {
				throw new InvalidArgumentException("numeric user ids expected for weibo user");
			}

			for ($i=1; $i <= $page; $i++) { 
				$crawl_url = 'https://m.weibo.cn/container/getIndex?type=uid&value='.$ei.'&containerid=107603'.$ei.'&page='.$i;

				$this->log("开始请求地址:$crawl_url");

				$this->snoopy->fetch($crawl_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$crawl_result = $this->snoopy->results;

				$this->log("请求返回结果:$crawl_result");

				$message_result = json_decode($crawl_result, true);

				if (!is_array($message_result)) {
					continue;
				}

				$message_result = $message_result['data'];

				if (isset($message_result['cards'])) {
					foreach ($message_result['cards'] as $rcc) {
						if ($rcc['card_type'] == 9) {
							$rcc['mblog']['created_at_time'] = date('Y-m-d H:i', $this->getWeiboMblogTime($rcc['mblog']['created_at']));
							$this->crawl_messages[] = $rcc['mblog'];
						}
					}
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
						if (mb_strpos($ssc['text'], $kf) !== false) {
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

	public function doImageCheck() {
		if (isset($this->crawl_config['image_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				//如果是视频，不检测图片限制
				if (isset($ssc['page_info']) && $ssc['page_info']['type'] == 'video') {
					continue;
				}
				if (!isset($ssc['pics']) || count($ssc['pics']) < $this->crawl_config['image_check']) {
					$this->log('不满足图片设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doVideoCheck() {
		if (isset($this->crawl_config['video_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if (!isset($ssc['page_info']) || $ssc['page_info']['type'] != 'video') {
					$this->log('不满足视频设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}