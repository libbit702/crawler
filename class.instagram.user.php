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
		if (!isset($this->crawl_config['ids']) || empty($this->crawl_config['ids'])) {
			throw new Exception("ids required for instagram user");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new Exception("invalid page setting for instagram user");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
		
		$this->snoopy->use_gzip = false; // Comment this line if you have php built with zlib

		foreach ($this->crawl_config['ids'] as $kw) {
			$last_id = null;//用于翻页请求中传入前一页数据的ID

			for ($i=1; $i <= $page; $i++) { 
				if ($i == 1) {
					$weibo_url = 'https://www.instagram.com/'.$kw.'/';

					$this->log("开始请求地址:$weibo_url");

					$this->snoopy->fetch($weibo_url);

					// 如果第一页没有拿到数据，就无法获取第二页的数据
					if ($this->snoopy->results === null) {
						break;
					}

					// $this->log("请求返回结果:".$this->snoopy->results);

					preg_match('#_sharedData = ([\s\S]+?)</script>#', $this->snoopy->results, $matches);
					$config = json_decode(trim($matches[1], ';'), true);

					$ins = $this->parseRenderData($config);
					
					if (empty($ins)) {
						break;
					}

					$this->crawl_messages = array_merge($this->crawl_messages,$ins);
					$last_id = $config['entry_data']['ProfilePage'][0]['user']['media']['page_info']['end_cursor'];
				} else {
					$weibo_url = 'https://www.instagram.com/graphql/query/?query_id=17888483320059182&variables='.rawurlencode(json_encode(array('id' => $config['entry_data']['ProfilePage'][0]['user']['id'], 'first' => 12, 'after' => $last_id)));

					$this->log("开始请求地址:$weibo_url");

					$this->snoopy->fetch($weibo_url);

					if ($this->snoopy->results === null) {
						break;
					}

					// $this->log("请求返回结果:".$this->snoopy->results);

					$result = json_decode($this->snoopy->results, true);

					foreach ($result['data']['user']['edge_owner_to_timeline_media']['edges'] as $node) {
						$this->crawl_messages[] = $node['node'];
					}
					
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
						if (mb_strpos($ssc['text'], $kf) !== false) {
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

	public function doImageCheck() {
		if (isset($this->crawl_config['image_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				//如果是视频，不检测图片限制
				if (isset($ssc['videos'])) {
					continue;
				}

				if (count($ssc['pics']) < $this->crawl_config['image_check']) {
					$this->log('不满足图片设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doVideoCheck() {
		if (isset($this->crawl_config['video_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if (!isset($ssc['videos'])) {
					$this->log('不满足视频设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doMessage() {
		print_r($this->crawl_messages);
	}

	private function parseRenderData($config){
		$nodes = $config['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
		$top_posts = $config['entry_data']['ProfilePage'][0]['user']['top_posts']['nodes'];

		if ($top_posts) {
			foreach ($top_posts as $key => $value) {
				array_unshift($nodes, $value);
			}
		}

		$fake_data = array();

		foreach ($nodes as $node) {
			$node['link'] = 'https://www.instagram.com/p/'.$node['code'].'/';
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