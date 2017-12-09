<?php

require_once('class.base.php');

class CrawlerDoulist extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("doulist ids required for douban list");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("doulist ids cannot be empty for douban list");
		}

		$page = isset($this->crawl_config['page']) ? intval($this->crawl_config['page']) : 0;
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for douban list");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Mobile Safari/537.36";

		foreach ($this->crawl_config['ids'] as $ei) {
			for ($i=0; $i <= ($page-1); $i++) { 
				$crawl_url = 'https://m.douban.com/doulist/'.$ei.'/?start='.$i * 25;

				$this->log("开始请求地址:$crawl_url");

				$this->snoopy->fetch($crawl_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$crawl_result = $this->snoopy->results;

				// $this->log("请求返回结果:$crawl_result");

				$content = str_get_html($crawl_result);
				$nodes = $content->find('.doulist-items li a');

				if (count($nodes) > 0) {
					foreach ($nodes as $node) {
						$rcc = array();
						$rcc['link'] = 'https://m.douban.com' . $node->getAttribute('href');
						$rcc['pics'] = array();
						
						$cover_image = $node->find('.cover img', 0);
						if ($cover_image) {
							$rcc['pics'][] = $cover_image->getAttribute('src');
						}

						$info = $node->find('.info', 0);
						if ($info) {
							$title_node = $info->find('.title', 0);
							if ($title_node) {
								$rcc['title'] = $title_node->innertext();
							}

							$rank_node = $info->find('.rating-stars', 0);
							if ($rank_node) {
								$rcc['rank'] = $rank_node->getAttribute('data-rating');
							}

							$meta_node = $info->find('.meta', 0);
							if ($meta_node) {
								$rcc['meta'] = trim($meta_node->innertext());
							}

							$recommend_node = $info->find('.recommend', 0);
							if ($recommend_node) {
								$rcc['recommend'] = $recommend_node->innertext();
							}
						}
						
						$this->crawl_messages[] = $rcc;
					}
				}
			}
		}
	}

	public function doKeywordCheck() {
		//豆列数据不检测关键字
	}

	public function doPublicTimeCheck() {
		//豆列数据不检测发布时间
	}

	public function doImageCheck() {
		//豆列数据不检测图片
	}

	public function doVideoCheck() {
		//豆列数据不检测视频
	}

	/**
	 * 经过过滤后的数据，可以做后续处理
	 */
	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}