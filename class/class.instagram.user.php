<?php

require_once('class.base.php');

class CrawlerInstagramUser extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids required for instagram user");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids cannot be empty for instagram user");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for instagram user");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
		
		$this->snoopy->use_gzip = false; // Comment this line if you have php built with zlib

		foreach ($this->crawl_config['ids'] as $kw) {
			$last_id = null;//用于翻页请求中传入前一页数据的ID

			for ($i=1; $i <= $page; $i++) { 

				if ($i == 1) {
					$crawl_url = 'https://www.instagram.com/'.$kw.'/';

					$this->log("开始请求地址:$crawl_url");

					$this->snoopy->fetch($crawl_url);

					// 如果第一页没有拿到数据，就无法获取第二页的数据
					if ($this->snoopy->results === null) {
						break;
					}

					$this->log("请求返回结果:".$this->snoopy->results);

					preg_match('#_sharedData = ([\s\S]+?);</script>#', $this->snoopy->results, $matches);
					$config = json_decode(trim($matches[1], ';'), true);
					
					$nodes = $config['entry_data']['ProfilePage']["0"]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
					// $top_posts = $config['entry_data']['TagPage'][0]['tag']['top_posts']['nodes'];
					// print_r($nodes);die();
					if ($top_posts) {
						foreach ($top_posts as $key => $value) {
							array_unshift($nodes, $value);
						}
					}

					$ins = $this->parseRenderData($nodes);
					
					if (empty($ins)) {
						break;
					}

					$this->crawl_messages = array_merge($this->crawl_messages,$ins);
					$last_id = $config['entry_data']['ProfilePage']["0"]['graphql']['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];

				} else {
					$crawl_url = 'https://www.instagram.com/graphql/query/?query_hash=42323d64886122307be10013ad2dcc44&variables='.rawurlencode(json_encode(array('id' => $config['entry_data']['ProfilePage']["0"]['graphql']['user']['id'], 'first' => 12, 'after' => $last_id)));

					$this->log("开始请求地址:$crawl_url");

					$this->snoopy->fetch($crawl_url);

					if ($this->snoopy->results === null) {
						break;
					}

					$this->log("请求返回结果:".$this->snoopy->results);

					$result = json_decode($this->snoopy->results, true);
					
					$nodes = $this->parseRenderData($result['data']['user']['edge_owner_to_timeline_media']['edges']);
					
					$this->crawl_messages = array_merge($this->crawl_messages, $nodes);
					
					$last_id = $result['data']['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];
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
						if (mb_strpos($ssc['caption'], $kf) !== false) {
							$this->log('正文筛出关键字匹配成功，删除数据:' . print_r($ssc, true));
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
		//Instagram数据不检测图片，不过有些ins的图片个数是大于1的，视频也是大于1的
	}
	*/

	public function doVideoCheck() {
		if (isset($this->crawl_config['video_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if (!isset($ssc['video'])) {
					$this->log('不满足视频设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}

	private function parseRenderData($nodes){
		$fake_data = array();

		foreach ($nodes as $node) {
			if (isset($node['node'])) {
				$node = $node['node'];
				$node['created_at_time'] = date('Y-m-d H:i:s', $node['taken_at_timestamp']);
			} else {
				$node['created_at_time'] = date('Y-m-d H:i:s', $node['date']);
			}
			
			$node['link'] = 'https://www.instagram.com/p/'.(isset($node['code']) ? $node['code'] : $node['shortcode']).'/';
			if ($node['is_video']) {
				$this->snoopy->fetch($node['link'] . '?__a=1');
				$video_content = $this->snoopy->results;
				if ($video_content === null) {
					continue;
				}
				$video = json_decode($video_content, true);
				$node['video'] = $video;
			} 
			$fake_data[] = $node;
		}
		return $fake_data;
	}
}