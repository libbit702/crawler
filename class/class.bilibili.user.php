<?php

require_once('class.base.php');

class CrawlerBilibiliUser extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids required for bilibili user");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids cannot be empty for bilibili user");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for bilibili user");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['ids'] as $ei) {
			if (!is_numeric($ei)) {
				throw new InvalidArgumentException("numeric user ids expected for bilibili user");
			}

			$member_url = 'http://space.bilibili.com/ajax/member/GetInfo';
			$this->snoopy->referer = 'http://space.bilibili.com/'.$ei.'/';
			$this->snoopy->submit($member_url, array('mid' => $ei));
			$member_info = $this->snoopy->results;
			$member_info = json_decode($member_info, true);

			for ($i=1; $i <= $page; $i++) { 
				$weibo_url = 'http://space.bilibili.com/ajax/member/getSubmitVideos?mid='.$ei.'&pagesize=20&tid=0&page='.$i;

				$this->log("开始请求地址:$weibo_url");

				$this->snoopy->fetch($weibo_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$weibo_result = $this->snoopy->results;

				$this->log("请求返回结果:$weibo_result");

				$result = json_decode($weibo_result, true);

				if (!is_array($result) || !isset($result['data']['vlist'])) {
					continue;
				}

				if (isset($result['data']['vlist'])) {
					foreach ($result['data']['vlist'] as $rcc) {
						$rcc['url_author'] = $member_info['data']['name'];
						$rcc['pic'] = 'http:' . $rcc['pic'];
						$rcc['pics'] = [$rcc['pic']];
						$rcc['created_at_time'] = date('Y-m-d H:i:s', $rcc['created']);
						$this->crawl_messages[] = $rcc;
					}
				}
			}
		}
	}

	public function doKeywordCheck() {
		//B站Up主数据不检测关键字
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
		//B站Up主数据不检测图片
	}

	public function doVideoCheck() {
		//B站Up主数据不检测视频
	}
	*/

	/**
	 * 经过过滤后的数据，可以做后续处理
	 */
	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}