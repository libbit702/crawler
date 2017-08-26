<?php

require_once('class.base.php');

class CrawlerBilibiliSearch extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['keywords']) || empty($this->crawl_config['keywords'])) {
			throw new Exception("keywords required for bilibili search");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new Exception("invalid page setting for bilibili search");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['keywords'] as $kw) {
			for ($i=1; $i <= $page; $i++) { 
				$weibo_url = 'http://search.bilibili.com/ajax_api/video?keyword='.rawurlencode($kw).'&page='.$i; 

				$this->log("开始请求地址:$weibo_url");

				$this->snoopy->fetch($weibo_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$weibo_result = $this->snoopy->results;

				$this->log("请求返回结果:$weibo_result");

				$result = json_decode($weibo_result, true);

				if (!is_array($result) || !isset($result['html'])) {
					continue;
				}

				$content = str_get_html($result['html']);
				$nodes = $content->find('li');
				foreach ($nodes as $node) {
					$obj = array();

					$ahref = $node->find('a', 0);
					$obj['link'] = 'http:' . $ahref->getAttribute('href');
					list($obj['link'],) = explode('?', $obj['link']);

					$obj['title'] = $ahref->getAttribute('title');

					$img = $node->find('img', 0);
					if ($img) {
						$obj['pics'] = array();
						if ($img->getAttribute('src')) {
							$obj['pics'][] = 'http:' . $img->getAttribute('src');
						} else {
							$obj['pics'][] = 'http:' . $img->getAttribute('data-src');
						}
						
					}

					$info = $node->find('.tags', 0);
					$created_at = $info->find('span.time', 0);
					if ($created_at) {
						$created_at = trim(strip_tags($created_at->innertext()));
						$obj['created_at_time'] = $created_at;
					}

					$url_author = $info->find('a.up-name', 0);
					if ($url_author) {
						$obj['url_author'] = $url_author->innertext();
						$obj['author_link'] = 'http:' . $url_author->getAttribute('href');
						list($obj['author_link'],) = explode('?', $obj['author_link']);
					}

					$playtime = $info->find('.watch-num', 0);
					if ($playtime) {
						$obj['playtime'] = trim(strip_tags($playtime->innertext()));
					}
					
					$this->crawl_messages[] = $obj;
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
				if (!isset($ssc['page_info']) || $ssc['page_info']['type'] != 'video') {
					$this->log('不满足视频设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doMessage() {
		print_r($this->crawl_messages);
	}
}