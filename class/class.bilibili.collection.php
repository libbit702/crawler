<?php

require_once('class.base.php');

class CrawlerBilibiliCollection extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("collection ids required for bilibili collection");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("collection ids cannot be empty for bilibili collection");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['ids'] as $ei) {
			$crawl_url = 'http://www.bilibili.com/video/'.$ei.'';

			$this->log("开始请求地址:$crawl_url");

			$this->snoopy->fetch($crawl_url);
			
			if ($this->snoopy->results === null) {
				continue;
			}

			$crawl_result = $this->snoopy->results;

			$this->log("请求返回结果:$crawl_result");

			preg_match('#<script>window.__INITIAL_STATE__=(.*?);\(function#', $crawl_result, $matches);

			if (!isset($matches[1]) || empty($matches[1])) {
				continue;
			}

			$matches[1] = json_decode($matches[1],true);

			$video_data = $matches[1];
			
			$url_author = $video_data['upData']['name'];
			$cover_image = $video_data['videoData']['pic'];

			$vlist = $video_data['videoData']['pages'];

			if (count($vlist) > 0) {
				foreach ($vlist as $node) {
					$rcc = array();
					$rcc['url_author'] = $url_author;
					$rcc['link'] = 'http://www.bilibili.com/video/av' . $video_data['aid'] . '?p=' . $node['page'];
					$rcc['title'] = $node['part'];
					$rcc['pics'] = array();
					$rcc['pics'][] = $cover_image;
					$this->crawl_messages[] = $rcc;
				}
			}
		
		}
	}

	/*
	public function doKeywordCheck() {
		//B站视频集数据不检测关键字
	}

	public function doPublicTimeCheck() {
		//B站视频集数据不检测发布时间
	}

	public function doImageCheck() {
		//B站视频集数据不检测图片
	}

	public function doVideoCheck() {
		//B站视频集数据不检测视频
	}
	*/

	/**
	 * 经过过滤后的数据，可以做后续处理
	 */
	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}