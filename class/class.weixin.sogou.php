<?php

require_once('class.base.php');

class CrawlerWeixinSogou extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['keywords'])) {
			throw new InvalidArgumentException("keywords required for weixin sogou");
		}

		if (empty($this->crawl_config['keywords'])) {
			throw new InvalidArgumentException("keywords cannot be empty for weixin sogou");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for weixin sogou");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		foreach ($this->crawl_config['keywords'] as $kw) {
			for ($i=1; $i <= $page; $i++) { 
				$weibo_url = 'http://weixin.sogou.com/weixin?type=2&ie=utf8&s_from=hotnews&query='.rawurlencode($kw).'&page='.$i; 

				$this->log("开始请求地址:$weibo_url");

				$this->snoopy->fetch($weibo_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$weibo_result = $this->snoopy->results;

				$this->log("请求返回结果:$weibo_result");

				$content = str_get_html($weibo_result);
				$nodes = $content->find('.news-list li');
				foreach ($nodes as $node) {
					$obj = [];

					$img_box = $node->find('.img-box', 0);
					$txt_box = $node->find('.txt-box', 0);
					$img_d_box = $node->find('.img-d', 0);

					if ($img_box) {
						$img = $img_box->find('img', 0);
						if ($img) {
							$obj['pics'] = [];
							$obj['pics'][] = htmlspecialchars_decode($img->getAttribute('src'));
						}
					} else {
						$imgs = $img_d_box->find('img');
						if ($imgs) {
							$obj['pics'] = [];
							foreach ($imgs as $img) {
								$obj['pics'][] = htmlspecialchars_decode($img->getAttribute('src'));
							}
						}
					}

					$title = $txt_box->find('h3 a', 0);
					if ($title) {
						$obj['title'] = strip_tags($title->innertext());
						$obj['link'] = htmlspecialchars_decode($title->getAttribute('href'));
					}

					$info = $txt_box->find('.txt-info', 0);
					if ($info) {
						$obj['info'] = strip_tags($info->innertext());
					}

					$created_at = $txt_box->find('.s-p', 0);
					if ($created_at) {
						$obj['created_at'] = date('Y-m-d H:i:s',$created_at->getAttribute('t'));
					}

					$url_author = $txt_box->find('a.account', 0);
					if ($url_author) {
						$obj['url_author'] = $url_author->innertext();
						$obj['author_link'] = htmlspecialchars_decode($url_author->getAttribute('href'));
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
		//微信搜索数据不检测图片
	}

	public function doVideoCheck() {
		//微信搜索数据不检测视频
	}
	*/

	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}